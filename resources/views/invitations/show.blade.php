<x-app-layout title="Invitation à rejoindre une colocation">
    <x-alert />
    <div style="max-width:600px;margin:0 auto;">
        <div class="card bg-white shadow-sm border border-gray-100 rounded-3xl" style="padding:40px 32px;text-align:center;">
            
            <div style="width:80px;height:80px;border-radius:50%;background:var(--sage-light, #F0F4F2);display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;">
                🏡
            </div>

            <h1 style="font-family:'DM Serif Display',serif;font-size:28px;margin-bottom:8px;color:var(--slate, #1B4332);">
                Invitation à rejoindre
            </h1>
            <h2 style="font-family:'DM Serif Display',serif;font-size:24px;color:var(--teal, #2D5A4C);margin-bottom:16px;">
                {{ $invitation->colocation->name }}
            </h2>

            <div style="background:var(--cream, #F9F8F3);padding:20px;border-radius:12px;margin-bottom:24px;text-align:left;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;font-size:20px;">👤</div>
                    <div>
                        <div style="font-size:12px;color:var(--muted, #9CA3AF);text-transform:uppercase;letter-spacing:0.05em;">Invité par</div>
                        <div style="font-weight:600;color:var(--slate, #1B4332);">{{ $invitation->colocation->owner->name }}</div>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;font-size:20px;">📧</div>
                    <div>
                        <div style="font-size:12px;color:var(--muted, #9CA3AF);text-transform:uppercase;letter-spacing:0.05em;">Email invité</div>
                        <div style="font-weight:600;color:var(--slate, #1B4332);">{{ $invitation->email }}</div>
                    </div>
                </div>
            </div>

            {{-- LOGIQUE DE VERIFICATION DIRECTEMENT DANS BLADE --}}
            
            @if(!Auth::check())
                {{-- 1. L'utilisateur n'est pas connecté --}}
                <div style="background:#DBEAFE;color:#1E40AF;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    🔐 Vous devez vous connecter avec l'adresse <strong>{{ $invitation->email }}</strong> pour accepter.
                </div>
                <a href="{{ route('login') }}" style="display:inline-block;background:#2D5A4C;color:white;padding:12px 24px;border-radius:10px;font-weight:bold;text-decoration:none;">Se connecter</a>

            @elseif(Auth::user()->email !== $invitation->email)
                {{-- 2. Mauvais compte email --}}
                <div style="background:#FEF3C7;color:#B45309;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    ⚠️ Cette invitation est destinée à <strong>{{ $invitation->email }}</strong>.<br>
                    Vous êtes connecté(e) en tant que {{ Auth::user()->email }}.
                </div>

            @elseif(Auth::user()->hasActiveColocation())
                {{-- 3. A déjà une colocation --}}
                <div style="background:#FEF3C7;color:#B45309;padding:16px;border-radius:10px;margin-bottom:24px;font-size:14px;">
                    🏠 Vous ne pouvez rejoindre qu'une seule colocation à la fois.
                </div>
                <a href="{{ route('colocations.index') }}" style="display:inline-block;background:#2D5A4C;color:white;padding:12px 24px;border-radius:10px;font-weight:bold;text-decoration:none;">Voir ma colocation</a>

            @else
                {{-- 4. Tout est parfait, on affiche les boutons d'action ! --}}
                

                <div style="display:flex;gap:12px;justify-content:center;">
                    {{-- Formulaire Accepter --}}
                    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                        @csrf
                        <button type="submit" style="background:#2D5A4C;color:white;padding:12px 24px;border-radius:10px;font-weight:bold;border:none;cursor:pointer;">Accepter</button>
                    </form>

                    {{-- Formulaire Refuser --}}
                    <form method="POST" action="{{ route('invitations.decline', $invitation->token) }}">
                        @csrf
                        <button type="submit" style="background:white;color:#6B7280;border:2px solid #E5E7EB;padding:12px 24px;border-radius:10px;font-weight:bold;cursor:pointer;">Refuser</button>
                    </form>
                </div>
            @endif

            <div style="margin-top:32px;padding-top:24px;border-top:2px solid var(--border, #E5E7EB);">
                <a href="{{ route('dashboard') }}" style="color:var(--muted, #9CA3AF);text-decoration:none;font-size:14px;font-weight:bold;">← Retour au tableau de bord</a>
            </div>

        </div>
    </div>
</x-app-layout>