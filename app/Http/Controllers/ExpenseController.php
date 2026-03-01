<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Colocation $colocation)
    {
        $isMember = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();
        
        if (!$isMember) {
            abort(403, 'Accès refusé.');
        }

        $expenses = $colocation->expenses()
            ->with(['category', 'payer'])
            ->orderBy('date', 'desc')
            ->get();

        $categories = Category::all();

        $activeMembers = $colocation->memberships()
            ->whereNull('left_at')
            ->with('user')
            ->get();

        return view('expenses.index', compact('colocation', 'expenses', 'categories', 'activeMembers'));
    }

    public function store(Request $request, Colocation $colocation)
    {
        $isMember = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();
        
        if (!$isMember) {
            abort(403, 'Seuls les membres actifs peuvent ajouter des dépenses.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'date' => 'required|date|before_or_equal:today',
            'category_id' => 'required|exists:categories,id',
            'payer_id' => 'required|exists:users,id',
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'date.required' => 'La date est obligatoire.',
            'date.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'category_id.required' => 'La catégorie est obligatoire.',
            'payer_id.required' => 'Le payeur est obligatoire.',
        ]);

        Expense::create([
            'coloc_id' => $colocation->id,
            'category_id' => $validated['category_id'],
            'payer_id' => $validated['payer_id'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Dépense ajoutée avec succès !');
    }

    public function edit(Colocation $colocation, Expense $expense)
    {
        $isMember = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();
        
        if (!$isMember) {
            abort(403, 'Accès refusé.');
        }

        $canEdit = ($expense->payer_id === Auth::id()) || ($colocation->owner_id === Auth::id());
        
        if (!$canEdit) {
            abort(403, 'Seul le payeur ou le propriétaire peut modifier cette dépense.');
        }

        $categories = Category::all();
        
        $activeMembers = $colocation->memberships()
            ->whereNull('left_at')
            ->with('user')
            ->get();

        return view('expenses.edit', compact('colocation', 'expense', 'categories', 'activeMembers'));
    }

    public function update(Request $request, Colocation $colocation, Expense $expense)
    {
        $canEdit = ($expense->payer_id === Auth::id()) || ($colocation->owner_id === Auth::id());
        
        if (!$canEdit) {
            abort(403, 'Seul le payeur ou le propriétaire peut modifier cette dépense.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'date' => 'required|date|before_or_equal:today',
            'category_id' => 'required|exists:categories,id',
            'payer_id' => 'required|exists:users,id',
        ]);

        $expense->update($validated);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Dépense modifiée avec succès !');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        $canDelete = ($expense->payer_id === Auth::id()) || ($colocation->owner_id === Auth::id());
        
        if (!$canDelete) {
            abort(403, 'Seul le payeur ou le propriétaire peut supprimer cette dépense.');
        }

        $expense->delete();

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Dépense supprimée avec succès !');
    }
}