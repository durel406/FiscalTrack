<?php

namespace Tests\Feature;

use App\Obligation;
use App\Services\FiscalSuiviService;
use App\TrackedDocType;
use Tests\TestCase;

class NotificationSyncTest extends TestCase
{
    public function test_sync_notifications_handles_missing_relations(): void
    {
        $type = TrackedDocType::create([
            'nom' => 'Test Type Inconnu',
            'periodicite' => 'mensuelle',
            'organisme_defaut' => 'DGID',
        ]);

        $obligation = Obligation::create([
            'contribuable_id' => null,
            'tracked_doc_type_id' => $type->id,
            'annee' => 2026,
            'periode' => 'M09',
            'date_limite' => now()->addDays(3)->toDateString(),
            'statut' => 'a_declarer',
            'organisme' => 'DGID',
        ]);

        $service = app(FiscalSuiviService::class);

        $created = $service->syncNotifications();

        $this->assertGreaterThanOrEqual(0, $created);
        $this->assertDatabaseHas('app_notifications', [
            'obligation_id' => $obligation->id,
        ]);
    }
}
