<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Settlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $validated = $request->validate([
            'creditor_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        Settlement::create([
            'coloc_id' => $colocation->id,
            'debtor_id' => Auth::id(), 
            'creditor_id' => $validated['creditor_id'], 
            'amount' => $validated['amount'],
            'date' => now(),
            'status' => 'paid',
        ]);

        return back()->with('success', 'Remboursement enregistré avec succès ! Le solde a été mis à jour.');
    }
}