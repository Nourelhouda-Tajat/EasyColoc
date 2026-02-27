<x-app-layout title="Mes colocations">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 class="page-title">Mes colocations</h1>
            <p class="page-subtitle">Gérez vos espaces de colocation</p>
        </div>
        @if(!auth()->user()->activeColocation)
            <a href="{{ route('colocations.create') }}" class="btn-primary-ec">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Créer une colocation
            </a>
        @endif
    </div>

    {{-- Info: une seule active --}}
    @if(auth()->user()->activeColocation)
        <div class="card" style="margin-bottom:24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <h3 style="font-family:'DM Serif Display',serif;font-size:18px;margin-bottom:4px;">📩 Inviter un membre</h3>
                    <p style="color:var(--muted);font-size:13px;">Invitez un utilisateur existant à rejoindre votre colocation</p>
                </div>
            </div>

            @if(session('success'))
                <div style="background:var(--sage-light);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
                    ✅ {{ session('success') }}
                </div>
            @endif

            @if($errors->has('email'))
                <div style="background:#FEE2E2;color:#B91C1C;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
                    ⚠️ {{ $errors->first('email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('colocations.invite.send', auth()->user()->activeColocation) }}" style="display:flex;gap:12px;">
                @csrf
                <input type="email" 
                    name="email" 
                    placeholder="Email de l'utilisateur à inviter" 
                    style="flex:1;padding:12px 16px;border:2px solid var(--border);border-radius:10px;font-size:14px;"
                    required
                    value="{{ old('email') }}">
                
                <button type="submit" class="btn-primary-ec" style="white-space:nowrap;">
                    Inviter
                </button>
            </form>

            <p style="font-size:12px;color:var(--muted);margin-top:12px;">
                💡 Seul un utilisateur déjà inscrit peut être invité.
            </p>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @forelse($colocations as $colocation)
            <a href="{{ route('colocations.show', $colocation) }}"
               class="coloc-card {{ $colocation->id === auth()->user()->active_colocation_id ? 'active-coloc' : '' }}">

                @if($colocation->id === auth()->user()->active_colocation_id)
                    <div style="position:absolute;top:16px;right:16px;background:var(--sage-light);color:var(--teal);font-size:11px;font-weight:700;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.05em;">Active</div>
                @endif

                <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:var(--sage-light);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🏡</div>
                    <div>
                        <div style="font-family:'DM Serif Display',serif;font-size:18px;letter-spacing:-0.3px;">{{ $colocation->name }}</div>
                        <div style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-top:2px;color:{{ $colocation->owner_id === auth()->id() ? 'var(--amber)' : 'var(--muted)' }};">
                            {{ $colocation->owner_id === auth()->id() ? '⭐ Owner' : 'Membre' }}
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
                    <div style="text-align:center;padding:10px;background:var(--cream);border-radius:10px;">
                        <div style="font-size:18px;font-weight:700;color:var(--slate);">{{ $colocation->members_count }}</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;">Membres</div>
                    </div>
                    <div style="text-align:center;padding:10px;background:var(--cream);border-radius:10px;">
                        <div style="font-size:15px;font-weight:700;color:var(--slate);">{{ number_format($colocation->total_expenses ?? 0, 0, ',', ' ') }}€</div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;">Dépenses</div>
                    </div>
                    <div style="text-align:center;padding:10px;background:var(--cream);border-radius:10px;">
                        @php $bal = $colocation->user_balance ?? 0; @endphp
                        <div style="font-size:15px;font-weight:700;color:{{ $bal >= 0 ? 'var(--sage)' : 'var(--coral)' }};">
                            {{ $bal >= 0 ? '+' : '' }}{{ number_format($bal, 0, ',', ' ') }}€
                        </div>
                        <div style="font-size:11px;color:var(--muted);margin-top:2px;">Mon solde</div>
                    </div>
                </div>
            </a>
        @empty
            <div class="card" style="grid-column:1/-1;text-align:center;padding:48px 20px;">
                <div style="font-size:48px;margin-bottom:16px;">🏠</div>
                <h3 style="font-family:'DM Serif Display',serif;font-size:20px;margin-bottom:8px;">Aucune colocation</h3>
                <p style="color:var(--muted);font-size:14px;margin-bottom:20px;">Créez votre première colocation ou rejoignez-en une via un lien d'invitation.</p>
                <a href="{{ route('colocations.create') }}" class="btn-primary-ec">Créer une colocation</a>
            </div>
        @endforelse

        {{-- Create card --}}
        @if(!auth()->user()->activeColocation)
            <a href="{{ route('colocations.create') }}"
               style="border-radius:16px;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer;padding:32px;transition:all 0.2s;background:white;text-decoration:none;"
               onmouseenter="this.style.borderColor='var(--sage-mid)'" onmouseleave="this.style.borderColor='var(--border)'">
                <div style="text-align:center;">
                    <div style="font-size:32px;margin-bottom:10px;">+</div>
                    <div style="font-weight:600;font-size:15px;color:var(--teal);">Créer une colocation</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:4px;">Ou rejoindre via invitation</div>
                </div>
            </a>
        @endif
    </div>

</x-app-layout>
