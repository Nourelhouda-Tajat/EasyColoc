<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Est-ce que l'utilisateur a une colocation active ?
        $activeMembership = Auth::user()->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', fn($q) => $q->where('status', 'active'))
            ->with('colocation')
            ->first();

        // 2. Si oui, on récupère la colocation
        $colocation = $activeMembership?->colocation;

        // 3. Stats simples (valeurs par défaut = 0)
        $stats = [
            'total_expenses' => 0,
            'user_balance' => 0,
            'members_count' => 0,
        ];

        // 4. Si on a une colocation, on met à jour les stats
        if ($colocation) {
            $stats['total_expenses'] = $colocation->expenses->sum('amount') ?? 0;
            $stats['members_count'] = $colocation->memberships->whereNull('left_at')->count();
        }

        // 5. Données pour la vue
        $inactiveColocations = Auth::user()->memberships()
            ->whereNotNull('left_at')
            ->with('colocation')
            ->get();

        $pendingInvitations = Invitation::where('email', Auth::user()->email)
            ->where('status', 'pending')
            ->get();

        // 6. Retour vers la vue
        return view('dashboard', compact(
            'activeMembership',
            'inactiveColocations',
            'pendingInvitations',
            'stats'
        ));
    }
}