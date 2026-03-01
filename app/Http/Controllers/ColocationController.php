<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = Auth::user()->memberships()
            ->with('colocation') 
            ->orderBy('joined_at', 'desc') 
            ->get();

        return view('colocations.index', compact('colocations'));
    }

    public function create()
    {
        if (Auth::user()->hasActiveColocation()) {
            return redirect()->route('colocations.index')
                ->withErrors(['error' => 'Attention, une seule colocation active à la fois']);
        }

        return view('colocations.create');
    }


    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            
            if (Auth::user()->hasActiveColocation()) {
                return back()->withErrors(['error' => 'Vous avez déjà une colocation active.']);
            }

            $validated = $request->validate([
                'name' => 'required|string|min:3|max:255',
            ]);

            $colocation = Colocation::create([
                'name' => $validated['name'],
                'status' => 'active',
                'owner_id' => Auth::id(), 
            ]);

            Membership::create([
                'user_id' => Auth::id(),
                'coloc_id' => $colocation->id,
                'role' => 'owner', 
                'joined_at' => now(),
                'left_at' => null,
            ]);

            return redirect()->route('colocations.show', $colocation)
                ->with('success', 'Colocation créée avec succès !');
        });

    }

    public function show(Request $request, Colocation $colocation)
    {
        $isMember = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();

        if (!$isMember) {
            abort(403, 'Accès refusé : vous n\'êtes pas membre de cette colocation.');
        }

        $activeMembers = $colocation->memberships()->with('user')->whereNull('left_at')->get();
        
        $selectedMonth = $request->query('month'); 
        $selectedYear = $request->query('year', now()->year);
        $filterTitle = $selectedMonth ? ucfirst(\Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y')) : 'Tous les mois';
        
        $expensesQuery = $colocation->expenses()->with(['category', 'payer'])->orderBy('date', 'desc');
        if ($selectedMonth) {
            $expensesQuery->whereMonth('date', $selectedMonth)
                          ->whereYear('date', $selectedYear);
        }
        $expenses = $expensesQuery->get();

        $totalExpenses = $colocation->expenses()->sum('amount');

        $categories = \App\Models\Category::whereNull('coloc_id')
            ->orWhere('coloc_id', $colocation->id)
            ->orderBy('name')
            ->get();

        $debtData = $colocation->calculateDebts();
        $suggestedSettlements = $debtData['suggested'];
        
        $userBalance = 0;
        if (isset($debtData['balances'][Auth::id()])) {
            $userBalance = $debtData['balances'][Auth::id()]['net'];
        }

        return view('colocations.show', compact(
            'colocation', 'activeMembers', 'totalExpenses', 'userBalance', 'categories', 
            'expenses', 'selectedMonth', 'selectedYear', 'filterTitle', 'suggestedSettlements'
        ));
    }
    
    public function update(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le owner peut modifier cette colocation.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $colocation->update($validated);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation mise à jour.');
    }

    public function leave(Colocation $colocation)
    {
        $user = Auth::user();

        if ($colocation->owner_id === $user->id) {
            return back()->withErrors(['error' => ' Vous devez annuler la colocation.']);
        }

        $membership = Membership::where('coloc_id', $colocation->id)
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->first();

        if (!$membership) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Vous ne faites plus partie de cette colocation.']);
        }

        $membership->update(['left_at' => now()]);


        return redirect()->route('dashboard')->with('success', 'Vous avez quitté la colocation avec succès.');
    }

    public function destroy(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le owner peut supprimer cette colocation.');
        }

        $colocation->update([
            'status' => 'inactive', 
        ]);
        $colocation->memberships()->update(['left_at' => now()]);

        return redirect()->route('colocations.index')
            ->with('success', 'Colocation terminé.');
    }

    public function removeMember(Colocation $colocation, User $member)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut retirer un membre.');
        }

        if ($member->id === Auth::id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas vous retirer vous-même. Utilisez le bouton "Annuler la coloc".']);
        }

        $membership = Membership::where('coloc_id', $colocation->id)
            ->where('user_id', $member->id)
            ->whereNull('left_at')
            ->first();

        if (!$membership) {
            return back()->withErrors(['error' => 'Ce membre ne fait pas partie de la colocation active.']);
        }

        $membership->update(['left_at' => now()]);

        return back()->with('success', $member->name . ' a été retiré de la colocation.');
    }
}
