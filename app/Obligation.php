<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Obligation extends Model
{
    protected $fillable = [
        'contribuable_id',
        'tracked_doc_type_id',
        'annee',
        'periode',
        'date_limite',
        'date_depot',
        'statut',
        'organisme',
        'resultat_controle',
        'commentaire_controle',
        'date_controle',
    ];

    protected $dates = ['date_limite', 'date_depot', 'date_controle'];

    const STATUT_A_DECLARER = 'a_declarer';
    const STATUT_DECLARE = 'declare';
    const STATUT_JUSTIFICATIF = 'justificatif_depose';

    public function contribuable()
    {
        return $this->belongsTo(Contribuable::class);
    }

    public function trackedDocType()
    {
        return $this->belongsTo(TrackedDocType::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    /** Pièce GED avec fichier réellement stocké */
    public function hasJustificatif()
    {
        return $this->documents()
            ->whereNotNull('fichier_path')
            ->whereNull('archived_at')
            ->exists();
    }

    public function isCloturee()
    {
        return in_array($this->statut, [self::STATUT_DECLARE, self::STATUT_JUSTIFICATIF], true);
    }

    /**
     * Statut effectif pour l'UI : ajoute "penalite" (retard) si échéance dépassée
     * et obligation non clôturée.
     */
    public function effectiveStatut()
    {
        if ($this->isCloturee()) {
            return $this->statut === self::STATUT_JUSTIFICATIF
                ? self::STATUT_JUSTIFICATIF
                : self::STATUT_DECLARE;
        }

        if ($this->date_limite && $this->date_limite->copy()->startOfDay()->lt(Carbon::today())) {
            return 'penalite';
        }

        return self::STATUT_A_DECLARER;
    }

    public function joursAvantEcheance()
    {
        if (! $this->date_limite) {
            return null;
        }

        return Carbon::today()->diffInDays($this->date_limite->copy()->startOfDay(), false);
    }

    /**
     * Propose une date limite selon périodicité (contexte fiscal Cameroun / cabinet).
     * Mensuelle : 15 du mois. Trimestrielle : +15 j fin de trimestre.
     * Annuelle : 15 mars (par défaut cabinet).
     */
    public static function suggestDeadline($annee, $periode, $periodicite = 'libre')
    {
        $annee = (int) $annee;
        $periodicite = $periodicite ?: 'libre';

        if (preg_match('/^T([1-4])$/i', $periode, $m)) {
            $ends = [1 => [3, 31], 2 => [6, 30], 3 => [9, 30], 4 => [12, 31]];
            [$month, $day] = $ends[(int) $m[1]];
            $end = Carbon::create($annee, $month, $day)->startOfDay();

            return $end->copy()->addDays(15)->toDateString();
        }

        if (preg_match('/^M(\d{2})$/i', $periode, $m)) {
            $month = max(1, min(12, (int) $m[1]));

            return Carbon::create($annee, $month, 15)->toDateString();
        }

        if (strtoupper($periode) === 'ANNUEL' || $periodicite === 'annuelle') {
            return Carbon::create($annee, 3, 15)->toDateString();
        }

        if ($periodicite === 'mensuelle') {
            return Carbon::create($annee, (int) date('n'), 15)->toDateString();
        }

        if ($periodicite === 'trimestrielle') {
            $q = (int) ceil(((int) date('n')) / 3);
            $periode = 'T'.$q;

            return self::suggestDeadline($annee, $periode, 'trimestrielle');
        }

        return null;
    }

    public function toFront()
    {
        $type = $this->trackedDocType;
        $contrib = $this->contribuable;
        $docs = $this->relationLoaded('documents')
            ? $this->documents
            : $this->documents()->actifs()->get();

        $hasFile = $docs->contains(function ($d) {
            return ! empty($d->fichier_path);
        });

        $effective = $this->effectiveStatut();
        $jours = $this->joursAvantEcheance();

        return [
            'id'                  => $this->id,
            'contribuable_id'     => $this->contribuable_id,
            'contribuable_nom'    => $contrib ? $contrib->nom : '—',
            'contribuable_niu'    => $contrib ? $contrib->niu : null,
            'contribuable_regime' => $contrib ? $contrib->regime : null,
            'contribuable_cat'    => $contrib ? $contrib->cat : null,
            'tracked_doc_type_id' => $this->tracked_doc_type_id,
            'type_nom'            => $type ? $type->nom : '—',
            'periodicite'         => $type ? ($type->periodicite ?: 'libre') : 'libre',
            'annee'               => (int) $this->annee,
            'periode'             => $this->periode,
            'periode_label'       => self::periodeLabel($this->periode),
            'date_limite'         => $this->date_limite ? $this->date_limite->format('Y-m-d') : null,
            'date_depot'          => $this->date_depot ? $this->date_depot->format('Y-m-d') : null,
            'statut'              => $this->statut,
            'statut_effectif'     => $effective,
            'organisme'           => $this->organisme,
            'resultat_controle'   => $this->resultat_controle,
            'commentaire_controle'=> $this->commentaire_controle,
            'date_controle'       => $this->date_controle ? $this->date_controle->format('Y-m-d') : null,
            'has_justificatif'    => $hasFile,
            'documents_count'     => $docs->count(),
            'document_ids'        => $docs->pluck('id')->values(),
            'jours_restants'      => $jours,
            'echeance_bucket'     => self::echeanceBucket($jours, $this->isCloturee()),
        ];
    }

    public static function periodeLabel($periode)
    {
        $p = strtoupper((string) $periode);
        if (preg_match('/^T([1-4])$/', $p, $m)) {
            return 'Trimestre '.$m[1];
        }
        if (preg_match('/^M(\d{2})$/', $p, $m)) {
            $months = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',
                7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];

            return $months[(int) $m[1]] ?? $periode;
        }
        if ($p === 'ANNUEL') {
            return 'Annuel';
        }

        return $periode ?: '—';
    }

    public static function echeanceBucket($jours, $cloturee)
    {
        if ($cloturee) {
            return 'ok';
        }
        if ($jours === null) {
            return 'sans_date';
        }
        if ($jours < 0) {
            return 'retard';
        }
        if ($jours === 0) {
            return 'aujourdhui';
        }
        if ($jours <= 7) {
            return 'proche';
        }

        return 'ok';
    }
}
