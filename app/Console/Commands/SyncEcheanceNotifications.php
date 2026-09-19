<?php

namespace App\Console\Commands;

use App\Services\FiscalSuiviService;
use Illuminate\Console\Command;

class SyncEcheanceNotifications extends Command
{
    protected $signature = 'fiscaltrack:echeances';

    protected $description = 'Détecte les échéances fiscales proches / dépassées et crée les notifications in-app';

    public function handle(FiscalSuiviService $suivi)
    {
        $created = $suivi->syncNotifications();
        $this->info("Notifications synchronisées ({$created} nouvelle(s)).");

        return 0;
    }
}
