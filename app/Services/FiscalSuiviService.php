<?php

namespace App\Services;

use App\AppNotification;
use App\Document;
use App\Obligation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FiscalSuiviService
{
    /**
     * KPIs de suivi fiscal pour le dashboard.
     */
    public function kpis()
    {
        $obligations = Obligation::with(['documents', 'trackedDocType', 'contribuable'])->get();

        $enAttente = 0;
        $enRetard = 0;
        $proches = 0;
        $aujourdhui = 0;
        $cloturees = 0;
        $sansPiece = 0;
        $avecPiece = 0;

        foreach ($obligations as $o) {
            $bucket = $o->toFront()['echeance_bucket'];
            $hasPiece = $o->hasJustificatif();

            if ($o->isCloturee()) {
                $cloturees++;
            } else {
                $enAttente++;
                if ($bucket === 'retard') {
                    $enRetard++;
                }
                if ($bucket === 'proche') {
                    $proches++;
                }
                if ($bucket === 'aujourdhui') {
                    $aujourdhui++;
                }
                if (! $hasPiece) {
                    $sansPiece++;
                }
            }

            if ($hasPiece) {
                $avecPiece++;
            }
        }

        $total = $obligations->count();
        $conformite = $total ? (int) round(($cloturees / $total) * 100) : null;

        return [
            'contribuables_actifs'   => \App\Contribuable::where('statut', 'active')->count(),
            'obligations_total'      => $total,
            'obligations_en_attente' => $enAttente,
            'obligations_en_retard'  => $enRetard,
            'obligations_proches'    => $proches,
            'obligations_aujourdhui' => $aujourdhui,
            'obligations_cloturees'  => $cloturees,
            'sans_justificatif'      => $sansPiece,
            'avec_justificatif'      => $avecPiece,
            'documents_ged'          => Document::actifs()->count(),
            'taux_conformite'        => $conformite,
            'notifications_non_lues' => AppNotification::unread()->count(),
        ];
    }

    /**
     * Synchronise les notifications in-app à partir des échéances d'obligations ouvertes.
     * Règle : J-7, J, retard — uniquement si non clôturée.
     */
    public function syncNotifications()
    {
        $today = Carbon::today();
        $created = 0;
        $openKeys = [];

        $obligations = Obligation::with(['contribuable', 'trackedDocType'])
            ->whereNotNull('date_limite')
            ->whereNotIn('statut', [Obligation::STATUT_DECLARE, Obligation::STATUT_JUSTIFICATIF])
            ->get();

        foreach ($obligations as $o) {
            $diff = $today->diffInDays($o->date_limite->copy()->startOfDay(), false);
            if ($diff > 7) {
                continue;
            }

            if ($diff >= 1) {
                $type = 'proche';
                $title = 'Échéance proche';
                $msg = sprintf(
                    '%s — %s (%s %d) arrive à échéance le %s (dans %d jour%s).',
                    $o->contribuable->nom,
                    $o->trackedDocType->nom,
                    $o->periode,
                    $o->annee,
                    $o->date_limite->format('d/m/Y'),
                    $diff,
                    $diff > 1 ? 's' : ''
                );
            } elseif ($diff === 0) {
                $type = 'aujourdhui';
                $title = "Échéance aujourd'hui";
                $msg = sprintf(
                    '%s — %s (%s %d) arrive à échéance aujourd\'hui.',
                    $o->contribuable->nom,
                    $o->trackedDocType->nom,
                    $o->periode,
                    $o->annee
                );
            } else {
                $type = 'retard';
                $late = abs($diff);
                $title = 'Échéance dépassée';
                $msg = sprintf(
                    '%s — %s (%s %d) en retard depuis %d jour%s (échéance du %s).',
                    $o->contribuable->nom,
                    $o->trackedDocType->nom,
                    $o->periode,
                    $o->annee,
                    $late,
                    $late > 1 ? 's' : '',
                    $o->date_limite->format('d/m/Y')
                );
            }

            $key = sprintf(
                'obl:%d|%s|%s',
                $o->id,
                $type,
                $o->date_limite->format('Y-m-d')
            );
            $openKeys[] = $key;

            $existing = AppNotification::where('dedupe_key', $key)->first();
            if ($existing) {
                $existing->message = $msg;
                $existing->title = $title;
                $existing->save();
                continue;
            }

            AppNotification::create([
                'obligation_id' => $o->id,
                'user_id'       => null,
                'type'          => $type,
                'title'         => $title,
                'message'       => $msg,
                'dedupe_key'    => $key,
            ]);
            $created++;
        }

        // Retirer les alertes obsolètes (obligation clôturée ou hors fenêtre)
        if (! empty($openKeys)) {
            AppNotification::whereNotIn('dedupe_key', $openKeys)
                ->whereIn('type', ['proche', 'aujourdhui', 'retard'])
                ->delete();
        } else {
            AppNotification::whereIn('type', ['proche', 'aujourdhui', 'retard'])->delete();
        }

        return $created;
    }

    public function listObligationsForFront()
    {
        return Obligation::with(['contribuable', 'trackedDocType', 'documents'])
            ->orderBy('date_limite')
            ->orderBy('annee', 'desc')
            ->get()
            ->map(function ($o) {
                return $o->toFront();
            })
            ->values();
    }

    public function listNotificationsForFront()
    {
        $this->syncNotifications();

        return AppNotification::with('obligation')
            ->orderByRaw("CASE type WHEN 'retard' THEN 0 WHEN 'aujourdhui' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($n) {
                return $n->toFront();
            })
            ->values();
    }

    /**
     * Maintient declaration_statuts en miroir pour compatibilité matrice héritée.
     */
    public function mirrorDeclarationStatut(Obligation $obligation)
    {
        if (! \Schema::hasTable('declaration_statuts')) {
            return;
        }

        $stored = $obligation->isCloturee() ? 'declare' : 'non_declare';

        DB::table('declaration_statuts')->updateOrInsert(
            [
                'contribuable_id'     => $obligation->contribuable_id,
                'tracked_doc_type_id' => $obligation->tracked_doc_type_id,
            ],
            [
                'statut'     => $stored,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
