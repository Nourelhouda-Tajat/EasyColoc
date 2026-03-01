<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $isActiveMember = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();

        if (!$isActiveMember) {
            abort(403, 'Seuls les membres actifs peuvent ajouter une dépense.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'payer_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $isPayerActiveMember = $colocation->memberships()
            ->where('user_id', $validated['payer_id'])
            ->whereNull('left_at')
            ->exists();

        if (!$isPayerActiveMember) {
            return back()->withErrors(['error' => 'Le payeur sélectionné n\'est pas un membre actif de cette colocation.']);
        }

        Expense::create([
            'coloc_id' => $colocation->id,
            'category_id' => $validated['category_id'],
            'payer_id' => $validated['payer_id'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Dépense ajoutée avec succès !');
    }

    public function update(Request $request, Colocation $colocation, Expense $expense)
    {
        if ($expense->coloc_id !== $colocation->id) {
            abort(404);
        }

        if (Auth::id() !== $expense->payer_id && Auth::id() !== $colocation->owner_id) {
            abort(403, 'Vous n\'avez pas le droit de modifier cette dépense.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id',
            'payer_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $expense->update($validated);

        return back()->with('success', 'Dépense mise à jour !');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        if ($expense->coloc_id !== $colocation->id) {
            abort(404);
        }

        if (Auth::id() !== $expense->payer_id && Auth::id() !== $colocation->owner_id) {
            abort(403, 'Vous n\'avez pas le droit de supprimer cette dépense.');
        }

        $expense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }
}