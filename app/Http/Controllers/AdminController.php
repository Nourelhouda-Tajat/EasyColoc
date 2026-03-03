<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Calcul des statistiques
        $stats = [
            'total_users' => User::count(),
            'banned_users' => User::where('is_banned', true)->count(),
            'total_colocations' => Colocation::count(),
            'active_colocations' => Colocation::where('status', 'active')->count(),
            'inactive_colocations' => Colocation::where('status', 'inactive')->count(),
            'total_expenses' => Expense::sum('amount'),
        ];

        // 2. Récupération de tous les utilisateurs (avec pagination pour que ce soit propre)
        // On exclut l'admin actuel pour éviter qu'il se banne lui-même par erreur !
        $users = User::where('id', '!=', auth()->id())->paginate(15);

        return view('admin.index', compact('stats', 'users'));
    }

    public function toggleBan(User $user)
    {
        // On inverse le statut actuel (Si c'était true, ça devient false, et inversement)
        $user->update([
            'is_banned' => !$user->is_banned
        ]);

        $message = $user->is_banned ? 'a été banni.' : 'a été débanni.';

        return back()->with('success', 'L\'utilisateur ' . $user->name . ' ' . $message);
    }
}