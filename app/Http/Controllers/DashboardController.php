<?php

namespace App\Http\Controllers;

use App\Models\Invitation; 
use App\Models\Colocation;
use App\Models\User;
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
            ->with('colocation.owner')
            ->get();

        if ($activeMembership) {
        $colocation = $activeMembership->colocation;
        
        $debtData = $colocation->calculateDebts();
        
        $stats['total_expenses'] = $colocation->expenses()->sum('amount');
        $stats['members_count'] = $colocation->memberships()->whereNull('left_at')->count();
        if (isset($debtData['balances'][Auth::user()->id])) {
            $stats['user_balance'] = $debtData['balances'][Auth::user()->id]['net'];
        }
    }
        $stats = [
            'total_expenses' => 0, 
            'user_balance' => 0,    
            'members_count' => $colocation ? $colocation->memberships->count() : 0,
        ];

        return view('dashboard', compact('activeMembership', 'inactiveColocations', 'pendingInvitations', 'stats'));

    }
}