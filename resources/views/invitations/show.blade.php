<x-app-layout title="Invitation à rejoindre une colocation">

    <div style="max-width:600px;margin:0 auto;">
        <div class="card" style="padding:40px 32px;text-align:center;">
            
            <div style="width:80px;height:80px;border-radius:50%;background:var(--sage-light);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;">
                🏡
            </div>

            <h1 style="font-family:'DM Serif Display',serif;font-size:28px;margin-bottom:8px;color:var(--slate);">
                Invitation à rejoindre
            </h1>
            <h2 style="font-family:'DM Serif Display',serif;font-size:24px;color:var(--teal);margin-bottom:16px;">
                {{ $invitation->colocation->name }}
            </h2>

            <div style="background:var(--cream);padding:20px;border-radius:12px;margin-bottom:24px;text-align:left;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;font-size:20px;">👤</div>
                    <div>
                        <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;">Invité par</div>
                        <div style="font-weight:600;color:var(--slate);">{{ $invitation->colocation->owner->name }}</div>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;font-size:20px;">📧</div>
                    <div>
                        <div style="font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em;">Email invité</div>
                        <div style="font-weight:600;color:var(--slate);">{{ $invitation->email }}</div>
                    </div>
                </div>
            </div>

            {{-- Messages d'état --}}
            @if($isExpired)
                <div style="background:#FEE2E2;color:#B91C1C;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ⚠️ Cette invitation a expiré.
                </div>

            @elseif($invitation->status === 'accepted')
                <div style="background:var(--sage-light);color:var(--teal);padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ✅ Cette invitation a déjà été acceptée.
                </div>

            @elseif($invitation->status === 'declined')
                <div style="background:#FEF3C7;color:#B45309;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ℹ️ Cette invitation a été refusée.
                </div>

            @elseif(!Auth::check())
                <div style="background:#DBEAFE;color:#1E40AF;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    🔐 Vous devez vous connecter pour accepter.
                </div>
                <a href="{{ route('login') }}" class="btn-primary-ec">Se connecter</a>

            @elseif(Auth::user()->email !== $invitation->email)
                <div style="background:#FEF3C7;color:#B45309;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ⚠️ Cette invitation est destinée à <strong>{{ $invitation->email }}</strong>.
                </div>

            @elseif(Auth::user()->hasActiveColocation())
                <div style="background:#FEF3C7;color:#B45309;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ⚠️ Vous ne pouvez rejoindre qu'une seule colocation à la fois.
                </div>
                <a href="{{ route('colocations.index') }}" class="btn-primary-ec">Voir ma colocation</a>

            @elseif($canAccept)
                <div style="background:var(--sage-light);color:var(--teal);padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ✅ Vous pouvez accepter cette invitation !
                </div>

                <div style="display:flex;gap:12px;justify-content:center;">
                    {{-- Accepter --}}
                    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                        @csrf
                        <button type="submit" class="btn-primary-ec">✅ Accepter</button>
                    </form>

                    {{-- Refuser --}}
                    <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}">
                        @csrf
                        <button type="submit" style="background:white;color:var(--muted);border:2px solid var(--border);padding:12px 24px;border-radius:10px;font-weight:600;cursor:pointer;">❌ Refuser</button>
                    </form>
                </div>
            @endif

            <div style="margin-top:32px;padding-top:24px;border-top:2px solid var(--border);">
                <a href="{{ route('colocations.index') }}" style="color:var(--muted);text-decoration:none;font-size:14px;">← Retour</a>
            </div>

        </div>
    </div>

</x-app-layout>