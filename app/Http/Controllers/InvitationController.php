<?php
namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvitationController extends Controller
{
    public function send(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut inviter.');
        }

        if ($colocation->status !== 'active') {
            return back()->withErrors(['error' => 'Cette colocation est annulée.']);
        }

        // 3. Valider l'email
        $validated = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = User::where('email', $validated['email'])->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Cet utilisateur n\'existe pas. Seuls les utilisateurs inscrits peuvent être invités.']);
        }

        $alreadyMember = $colocation->memberships()
            ->where('user_id', $user->id)
            ->whereNull('left_at')
            ->exists();
            
        if ($alreadyMember) {
            return back()->withErrors(['email' => 'Cet utilisateur est déjà membre de cette colocation.']);
        }

        $existingInvitation = Invitation::where('email', $validated['email'])
            ->where('coloc_id', $colocation->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existingInvitation) {
            return back()->withErrors(['email' => 'Une invitation est déjà en attente pour cet utilisateur.']);
        }

        $token = Str::uuid()->toString();
        
        Invitation::create([
            'email' => $validated['email'],
            'token' => $token,
            'status' => 'pending',
            'coloc_id' => $colocation->id,
            'expires_at' => Carbon::now()->addDays(7),
        ]);


        return back()->with('success', 'Invitation envoyée à ' . $validated['email']);
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)->first();
        
        if (!$invitation) {
            abort(404, 'Invitation introuvable.');
        }

        $invitation->load(['colocation.owner']);

        $isExpired = $invitation->expires_at && $invitation->expires_at->isPast();
        
        $isPending = $invitation->status === 'pending';

        $canAccept = false;
        if (Auth::check()) {
            $canAccept = (Auth::user()->email === $invitation->email) 
                      && !Auth::user()->hasActiveColocation()
                      && $isPending 
                      && !$isExpired;
        }

        return view('invitations.show', compact('invitation', 'canAccept', 'isExpired'));
    }

    public function accept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        
        // Vérifications de sécurité
        if ($invitation->status !== 'pending') {
            return redirect()->route('colocations.index')->withErrors('Cette invitation a déjà été traitée.');
        }
        
        if ($invitation->expires_at && $invitation->expires_at->isPast()) {
            return redirect()->route('colocations.index')->withErrors('Cette invitation a expiré.');
        }
        
        if (Auth::user()->email !== $invitation->email) {
            return redirect()->route('colocations.index')->withErrors('Cette invitation ne vous est pas destinée.');
        }
        
        if (Auth::user()->hasActiveColocation()) {
            return redirect()->route('colocations.index')
                ->withErrors('Vous ne pouvez rejoindre qu\'une seule colocation à la fois.');
        }

        Membership::create([
            'user_id' => Auth::id(),
            'coloc_id' => $invitation->coloc_id,
            'role' => 'member',
            'joined_at' => now(),
            'left_at' => null,
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $invitation->coloc_id)
            ->with('success', 'Bienvenue dans la colocation !');
    }

    public function decline(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        
        if ($invitation->status === 'pending' && Auth::user()->email === $invitation->email) {
            $invitation->update(['status' => 'declined']);
        }

        return redirect()->route('colocations.index')->with('info', 'Invitation refusée.');
    }
}