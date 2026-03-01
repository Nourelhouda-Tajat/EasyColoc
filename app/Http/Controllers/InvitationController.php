<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use App\Mail\ColocationInviteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class InvitationController extends Controller
{
    public function send(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== Auth::id()) {
            abort(403, 'Seul le propriétaire peut inviter des membres.');
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        $invitation = Invitation::create([
            'email' => $request->email,
            'token' => Str::random(32), 
            'status' => 'pending',
            'coloc_id' => $colocation->id,
        ]);

        Mail::to($request->email)->send(new ColocationInviteMail($invitation));

        return back()->with('success', 'Invitation envoyée avec succès à ' . $request->email);
    }

    public function show($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Cette invitation n\'est plus valide.']);
        }

        return view('invitations.show', compact('invitation'));
    }

    public function accept(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        $user = Auth::user();

        if ($user->hasActiveColocation()) {
            return redirect()->route('dashboard')
                ->withErrors(['error' => 'Vous avez déjà une colocation active. Vous devez la quitter avant d\'en rejoindre une autre.']);
        }

        DB::transaction(function () use ($invitation, $user) {
            $invitation->update(['status' => 'accepted']);

            Membership::create([
                'user_id' => $user->id,
                'coloc_id' => $invitation->coloc_id,
                'role' => 'member',
                'joined_at' => now(),
            ]);
        });

        return redirect()->route('colocations.show', $invitation->coloc_id)
            ->with('success', 'Bienvenue dans votre nouvelle colocation !');
    }

    public function decline(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->status !== 'pending') {
            return redirect()->route('dashboard')->withErrors(['error' => 'Cette invitation a déjà été traitée.']);
        }

        $invitation->update(['status' => 'declined']);

        return redirect()->route('dashboard')
            ->with('success', 'Vous avez refusé l\'invitation.');
    }
}