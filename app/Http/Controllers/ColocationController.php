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
        // On récupère les colocations avec leurs relations pour éviter de surcharger la base de données
        $colocations = Auth::user()->memberships()
            ->with(['colocation.memberships', 'colocation.expenses']) 
            ->orderBy('joined_at', 'desc') 
            ->get();

        // MVC : Préparation des données pour la vue
        foreach ($colocations as $membership) {
            $coloc = $membership->colocation;
            
            // Est-ce que la coloc est active ET que l'utilisateur y est encore ?
            $membership->is_active = ($coloc->status === 'active' && is_null($membership->left_at));
            
            // Calculs dynamiques
            $membership->members_count = $coloc->memberships->whereNull('left_at')->count();
            $membership->total_expenses = $coloc->expenses->sum('amount');
        }

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
        $currentUserId = Auth::id();

        // 1. SÉCURITÉ : Vérifier si l'utilisateur est membre actif (plus court)
        if (!$colocation->memberships()->where('user_id', $currentUserId)->whereNull('left_at')->exists()) {
            abort(403, 'Accès refusé : vous n\'êtes pas membre de cette colocation.');
        }

        // 2. DONNÉES DE BASE : Membres et Catégories
        $activeMembers = $colocation->memberships()->with('user')->whereNull('left_at')->get();
        $categories = Category::whereNull('coloc_id')->orWhere('coloc_id', $colocation->id)->get();

        // 3. LE FILTRE MENSUEL
        $selectedMonth = $request->month; // Raccourci Laravel pour $request->query('month')
        $selectedYear = $request->year ?? now()->year; // '??' signifie "ou bien l'année actuelle si c'est vide"
        
        $filterTitle = $selectedMonth 
            ? ucfirst(\Carbon\Carbon::create($selectedYear, $selectedMonth, 1)->translatedFormat('F Y')) 
            : 'Tous les mois';

        // Liste des 12 mois (Simplifiée avec now()->month($m))
        $filterMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $filterMonths[] = [
                'num' => str_pad($m, 2, '0', STR_PAD_LEFT), // Ajoute un zéro : "01", "02"...
                'name' => ucfirst(now()->month($m)->translatedFormat('F')), 
            ];
        }

        // 4. LES DÉPENSES
        $expensesQuery = $colocation->expenses()->with(['category', 'payer'])->orderBy('date', 'desc');
        
        if ($selectedMonth) {
            $expensesQuery->whereMonth('date', $selectedMonth)->whereYear('date', $selectedYear);
        }
        
        $expenses = $expensesQuery->get();
        $totalExpenses = $colocation->expenses()->sum('amount');

        // On indique à la vue si c'est nous qui avons payé
        foreach ($expenses as $expense) {
            $expense->is_payer = ($expense->payer_id === $currentUserId);
        }

        // 5. LES MATHÉMATIQUES (Dettes)
        $debtData = $colocation->calculateDebts();
        $suggestedSettlements = $debtData['suggested'];
        
        // Astuce très pratique : '?? 0' renvoie 0 si l'utilisateur n'est pas trouvé dans le tableau !
        $userBalance = $debtData['balances'][$currentUserId]['net'] ?? 0;

        // 6. ENVOI À LA VUE
        return view('colocations.show', compact(
            'colocation', 'activeMembers', 'totalExpenses', 'userBalance', 'categories', 
            'expenses', 'selectedMonth', 'selectedYear', 'filterTitle', 'suggestedSettlements', 'filterMonths'
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
        $debtData = $colocation->calculateDebts();
        $net = $debtData['balances'][$user->id]['net'] ?? 0;

        if ($net < 0) {
            $user->decrement('reputation'); // Il part avec des dettes : -1
        } else {
            $user->increment('reputation'); // Il part clean : +1
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

        $debtData = $colocation->calculateDebts();
        $net = $debtData['balances'][$member->id]['net'] ?? 0;

        if ($net < 0) {
            $member->decrement('reputation'); // Pénalité : -1

            // Transfert de la dette à l'Owner
            \App\Models\Settlement::create([
                'coloc_id' => $colocation->id,
                'debtor_id' => $member->id,             // Le membre expulsé "paie" virtuellement...
                'creditor_id' => $colocation->owner_id, // ...à l'Owner.
                'amount' => abs($net),
                'date' => now(),
                'status' => 'paid',
            ]);
        } else {
            $member->increment('reputation'); // Il est expulsé mais ne devait rien : +1
        }

        $membership->update(['left_at' => now()]);

        return back()->with('success', $member->name . ' a été retiré de la colocation.');
    }
}