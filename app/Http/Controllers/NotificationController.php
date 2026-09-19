<?php

namespace App\Http\Controllers;

use App\AppNotification;
use App\Services\FiscalSuiviService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $suivi;

    public function __construct(FiscalSuiviService $suivi)
    {
        $this->middleware('auth');
        $this->suivi = $suivi;
    }

    public function index()
    {
        $data = DeclarationController::donneesSuivi();

        return view('notifications.index', $data);
    }

    public function listJson()
    {
        return response()->json([
            'notifications' => $this->suivi->listNotificationsForFront(),
        ]);
    }

    public function markRead($id)
    {
        $n = AppNotification::findOrFail($id);
        $n->read_at = now();
        $n->save();

        return response()->json(['notification' => $n->toFront()]);
    }

    public function markAllRead()
    {
        AppNotification::unread()->update(['read_at' => now()]);

        return response()->json([
            'notifications' => $this->suivi->listNotificationsForFront(),
        ]);
    }
}
