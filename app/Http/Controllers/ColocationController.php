<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = Auth::user()->memberships()
            ->whereNull('left_at')
            ->whereHas('colocation', function ($query) {
                $query->where('status', 'active');
            })
            ->with('colocation.owner') 
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

    public function show(Colocation $colocation)
    {
        $isMember = $colocation->memberships()
            ->where('user_id', Auth::id())
            ->whereNull('left_at')
            ->exists();

        if ($isMember) {
            abort(403, 'Vous n\'avez pas accès à cette colocation.');
        }
        $colocation->load(['owner']);
        $activeMembers = $colocation->memberships()
            ->whereNull('left_at')
            ->with('user')
            ->get();
        return view('colocations.show', compact('colocation','activeMembers'));
    }

  
    public function edit(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le créateur peut modifier cette colocation.');
        }

        if ($colocation->status !== 'active') {
            return redirect()->route('colocations.show', $colocation)
                ->withErrors(['error' => 'Cette colocation a été annulée.']);
        }

        return view('colocations.edit', compact('colocation'));
    }

    
    public function update(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le créateur peut modifier cette colocation.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|min:3',
        ]);

        $colocation->update($validated);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation mise à jour.');
    }

    public function destroy(Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le créateur peut supprimer cette colocation.');
        }

        $colocation->update([
            'status' => 'inactive', 
        ]);
        $colocation->memberships()->update(['left_at' => now()]);

        return redirect()->route('colocations.index')
            ->with('success', 'Colocation terminé.');
    }
}
