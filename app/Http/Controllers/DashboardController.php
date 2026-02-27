<?php

namespace App\Http\Controllers;

use App\Models\Invitation; 
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $activeMembership = Auth::user()->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['colocation.owner', 'colocation.memberships' => function($q) {
                $q->whereNull('left_at');
            }])
            ->first();
        
        $inactiveColocations = Auth::user()->memberships()
            ->whereNotNull('left_at')
            ->orWhereHas('colocation', function ($query) {
                $query->where('status', 'inactive');
            })
            ->with('colocation')
            ->get();
        
        $pendingInvitations = Invitation::where('email', Auth::user()->email)
            ->where('status', 'pending')
            // ->where('expires_at', '>', now())
            ->with('colocation.owner')
            ->get();

        $colocation = $activeMembership ? $activeMembership->colocation : null;

        
        $stats = [
            'total_expenses' => 1248.00, // À dynamiser plus tard
            'user_balance' => -87.40,    // À dynamiser plus tard
            'members_count' => $colocation ? $colocation->memberships->count() : 0,
        ];

        return view('dashboard', compact('activeMembership', 'inactiveColocations', 'pendingInvitations', 'stats'));

    }
}