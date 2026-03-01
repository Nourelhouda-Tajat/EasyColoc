<x-app-layout>
    <x-alert />
    <nav class="text-xs font-medium text-gray-400 mb-4 px-2">
        Dashboard › <span class="text-[#1B4332]">{{ $colocation->name }}</span>
    </nav>

    <div class="flex justify-between items-center mb-8 px-2">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-sm border border-gray-100">🏠</div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-serif-custom text-[#1B4332]">{{ $colocation->name }}</h1>
                    <span class="bg-[#F0F4F2] text-[#1B4332] text-[10px] font-bold px-3 py-1 rounded-full uppercase">Active</span>
                </div>
                <p class="text-gray-400 text-sm mt-1">
                    {{ $activeMembers->count() }} membres · Créée le {{ $colocation->created_at->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>
        <div class="flex gap-3">
            {{-- On vérifie le rôle de l'utilisateur par rapport à la colocation --}}
            @if(Auth::id() === $colocation->owner_id)
                {{-- Bouton pour le OWNER (Annuler tout) --}}
                <form action="{{ route('colocations.destroy', $colocation) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler toute la colocation ?')">
                    @csrf @method('DELETE')
                    <button class="px-5 py-2.5 rounded-xl border border-red-100 text-red-500 font-bold text-sm hover:bg-red-50 transition">Annuler la coloc</button>
                </form>
            @else
                {{-- Bouton pour le MEMBER (Quitter) --}}
                <form action="{{ route('colocations.leave', $colocation) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment quitter cette colocation ?')">
                    @csrf
                    <button class="px-5 py-2.5 rounded-xl border border-orange-100 text-orange-500 font-bold text-sm hover:bg-orange-50 transition">Quitter la coloc</button>
                </form>
            @endif

            {{-- Bouton Ajouter Dépense (visible par tous les membres actifs) --}}
            <button onclick="document.getElementById('add-expense-form').classList.toggle('hidden')" class="bg-[#2D5A4C] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#2D5A4C]/20 hover:bg-[#1B4332] transition">
                + Ajouter dépense
            </button>
        </div>
    </div>

    {{-- Formulaire ajout dépense (caché par défaut) --}}
    <div id="add-expense-form" class="hidden bg-white p-6 rounded-3xl mb-8 border border-gray-100">
        <h3 class="text-lg font-bold text-[#1B4332] mb-4">Ajouter une dépense</h3>
        <form action="{{ route('colocations.expenses.store', $colocation) }}" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            <input type="text" name="title" placeholder="Titre de la dépense" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            <input type="number" step="0.01" name="amount" placeholder="Montant (€)" required class="bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            <select name="category_id" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
                <option value="">Sélectionnez une catégorie</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="payer_id" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
                <option value="">Qui a payé ?</option>
                @foreach($activeMembers as $member)
                    <option value="{{ $member->user_id }}" {{ $member->user_id === Auth::id() ? 'selected' : '' }}>
                        {{ $member->user->name }}
                    </option>
                @endforeach
            </select>
            <div class="col-span-2 flex gap-3">
                <button type="button" onclick="document.getElementById('add-expense-form').classList.add('hidden')" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50">Annuler</button>
                <button type="submit" class="flex-1 bg-[#2D5A4C] text-white px-4 py-3 rounded-xl font-bold text-sm hover:bg-[#1B4332]">Ajouter</button>
            </div>
        </form>
    </div>

    <div class="bg-[#2D5A4C] rounded-[32px] p-8 text-white mb-10 flex justify-between items-center shadow-xl shadow-[#2D5A4C]/10">
        <div>
            <p class="text-white/60 text-xs font-bold uppercase tracking-widest mb-2">Mon solde actuel</p>
            <h2 class="text-5xl font-bold">{{ number_format($userBalance ?? -87.50, 2, ',', ' ') }} €</h2>
            <p class="text-white/40 text-xs mt-4 italic font-medium">
                @if(($userBalance ?? 0) < 0)
                    Vous devez de l'argent à vos colocataires
                @elseif(($userBalance ?? 0) > 0)
                    Vos colocataires vous doivent de l'argent
                @else
                    Tout est soldé
                @endif
            </p>
        </div>
        <div class="text-right">
            <div class="bg-white/10 px-4 py-1.5 rounded-full text-[10px] font-bold inline-block mb-4">Janvier 2026</div>
            <p class="text-white/40 text-[10px] font-bold uppercase">Total dépenses</p>
            <p class="text-2xl font-bold">{{ number_format($totalExpenses ?? 1248.00, 2, ',', ' ') }} €</p>
        </div>
    </div>

    {{-- ONGLETS - Navigation --}}
    <div class="flex gap-8 border-b border-gray-100 mb-8 px-2">
        <button onclick="showTab('members')" id="btn-members" class="tab-btn pb-4 text-[#1B4332] font-bold text-sm border-b-2 border-[#1B4332] flex items-center gap-2 transition cursor-pointer">
            <span>👥</span> Membres ({{ $activeMembers->count() }})
        </button>
        <button onclick="showTab('expenses')" id="btn-expenses" class="tab-btn pb-4 text-gray-400 font-bold text-sm hover:text-[#1B4332] transition cursor-pointer">
            <span>💰</span> Dépenses ({{ $colocation->expenses->count() }})
        </button>
        <button onclick="showTab('debts')" id="btn-debts" class="tab-btn pb-4 text-gray-400 font-bold text-sm hover:text-[#1B4332] transition cursor-pointer">
            <span>⚖️</span> Qui doit à qui
        </button>
    </div>

    {{-- CONTENU Onglet Membres --}}
    <div id="tab-members" class="tab-content">
        @if(Auth::id() === $colocation->owner_id)
        <div class="bg-white border-2 border-dashed border-[#F0F4F2] rounded-[24px] p-8 mb-8">
            <h3 class="text-[#1B4332] font-serif-custom text-lg mb-2">Inviter un colocataire</h3>
            <p class="text-gray-400 text-xs mb-6">Envoyez une invitation par email. La personne recevra un lien pour rejoindre.</p>
            
            <form action="{{ route('colocations.invite.send', $colocation) }}" method="POST" class="flex gap-4">
                @csrf
                <input type="email" name="email" placeholder="email@exemple.fr" required
                       class="flex-1 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
                <button type="submit" class="bg-[#2D5A4C] text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-[#1B4332] transition">
                    Envoyer l'invitation
                </button>
            </form>
        </div>
        @endif

        <div class="space-y-4">
            @foreach($activeMembers as $membership)
            <div class="bg-white p-5 rounded-3xl flex items-center justify-between border border-gray-50 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#F9F8F3] rounded-full flex items-center justify-center font-bold text-[#1B4332]">
                        {{ strtoupper(substr($membership->user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-[#1B4332]">{{ $membership->user->name }} @if($membership->user_id === Auth::id()) (vous) @endif</h4>
                        <p class="text-[11px] text-gray-400">{{ $membership->user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-[#FFF8F0] text-orange-400 text-[9px] font-bold px-3 py-1 rounded-lg uppercase tracking-widest border border-orange-50">
                        {{ $membership->role }}
                    </span>
                    <span class="bg-[#F0FDF4] text-green-600 text-[9px] font-bold px-3 py-1 rounded-lg uppercase border border-green-50">
                        ★ +8 Excellent
                    </span>
                    
                    {{-- BOUTON RETIRER --}}
                    @if(Auth::id() === $colocation->owner_id && $membership->user_id !== Auth::id())
                        <form action="{{ route('colocations.members.remove', ['colocation' => $colocation->id, 'member' => $membership->user_id]) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment retirer {{ $membership->user->name }} de la colocation ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-red-100">
                                Retirer
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- CONTENU Onglet Dépenses --}}
    <div id="tab-expenses" class="tab-content hidden">
        <div class="flex gap-2 mb-6">
            <button class="px-4 py-2 rounded-lg bg-[#F0F4F2] text-[#1B4332] text-xs font-bold">Tous</button>
            <button class="px-4 py-2 rounded-lg text-gray-400 text-xs font-bold hover:bg-gray-50">Janvier</button>
            <button class="px-4 py-2 rounded-lg text-gray-400 text-xs font-bold hover:bg-gray-50">Décembre</button>
            <button class="px-4 py-2 rounded-lg text-gray-400 text-xs font-bold hover:bg-gray-50">Novembre</button>
        </div>

        <div class="space-y-3">
            @php
                $expenses = $colocation->expenses()->with(['category', 'payer'])->orderBy('date', 'desc')->get();
            @endphp
            
            @forelse($expenses as $expense)
            <div class="bg-white p-5 rounded-2xl border border-gray-50 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 rounded-full {{ $expense->payer_id === Auth::id() ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                    <div>
                        <h4 class="font-bold text-[#1B4332]">{{ $expense->title }}</h4>
                        <p class="text-xs text-gray-400">{{ $expense->date->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-[#1B4332]">{{ number_format($expense->amount, 2, ',', ' ') }} €</p>
                    <p class="text-[10px] {{ $expense->payer_id === Auth::id() ? 'text-green-500' : 'text-red-400' }}">
                        @if($expense->payer_id === Auth::id())
                            +{{ number_format($expense->amount / $activeMembers->count(), 2, ',', ' ') }} € à récupérer
                        @else
                            -{{ number_format($expense->amount / $activeMembers->count(), 2, ',', ' ') }} € à régler
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-[#F9F8F3] text-gray-500 text-[9px] font-bold px-2 py-1 rounded uppercase">
                        {{ $expense->category->name ?? 'DIVERS' }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <p class="text-4xl mb-2">💰</p>
                <p>Aucune dépense enregistrée</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- CONTENU Onglet Qui doit à qui --}}
    <div id="tab-debts" class="tab-content hidden">
        <div class="bg-orange-50 border border-orange-100 rounded-2xl p-4 mb-6 flex items-center gap-3">
            <span class="text-orange-400">⚡</span>
            <p class="text-xs text-orange-600 font-medium">Les dettes sont calculées automatiquement pour minimiser le nombre de transactions.</p>
        </div>

        <div class="space-y-3">
            {{-- Dette 1 --}}
            <div class="bg-white p-5 rounded-2xl border-l-4 border-red-400 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-2">
                        <div class="w-10 h-10 bg-[#F9F8F3] rounded-full flex items-center justify-center font-bold text-xs border-2 border-white text-[#1B4332]">AD</div>
                        <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center font-bold text-xs text-white border-2 border-white">BM</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#1B4332]">Vous devez à Bob Martin</h4>
                        <p class="text-xs text-gray-400">EDF + Courses (janvier)</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-red-500">47,63 €</span>
                    <button class="bg-green-50 text-green-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-green-100 transition">
                        Marquer payé
                    </button>
                </div>
            </div>

            {{-- Dette 2 --}}
            <div class="bg-white p-5 rounded-2xl border-l-4 border-red-400 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-2">
                        <div class="w-10 h-10 bg-[#F9F8F3] rounded-full flex items-center justify-center font-bold text-xs border-2 border-white text-[#1B4332]">AD</div>
                        <div class="w-10 h-10 bg-purple-400 rounded-full flex items-center justify-center font-bold text-xs text-white border-2 border-white">CL</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#1B4332]">Vous devez à Clara Leroy</h4>
                        <p class="text-xs text-gray-400">Produits ménagers</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-red-500">39,87 €</span>
                    <button class="bg-green-50 text-green-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-green-100 transition">
                        Marquer payé
                    </button>
                </div>
            </div>

            {{-- Dette 3 --}}
            <div class="bg-white p-5 rounded-2xl border-l-4 border-green-400 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-2">
                        <div class="w-10 h-10 bg-orange-400 rounded-full flex items-center justify-center font-bold text-xs text-white border-2 border-white">DP</div>
                        <div class="w-10 h-10 bg-[#F9F8F3] rounded-full flex items-center justify-center font-bold text-xs border-2 border-white text-[#1B4332]">AD</div>
                    </div>
                    <div>
                        <h4 class="font-bold text-[#1B4332]">David Petit vous doit</h4>
                        <p class="text-xs text-gray-400">Courses + Raclette</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-green-600">32,10 €</span>
                    <button class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-200 transition">
                        Confirmer reçu
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-6 bg-[#F9F8F3] p-5 rounded-2xl border border-gray-100">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm font-bold text-[#1B4332]">Bilan net</p>
                    <p class="text-xs text-gray-400">Après toutes les transactions, vous serez à 0 €</p>
                </div>
                <span class="text-xl font-bold text-red-500">-55,40 € net</span>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT pour les onglets --}}
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.add('hidden');
            });
            
            document.querySelectorAll('.tab-btn').forEach(function(btn) {
                btn.classList.remove('text-[#1B4332]', 'border-b-2', 'border-[#1B4332]');
                btn.classList.add('text-gray-400');
            });
            
            document.getElementById('tab-' + tabName).classList.remove('hidden');
            
            var activeBtn = document.getElementById('btn-' + tabName);
            activeBtn.classList.remove('text-gray-400');
            activeBtn.classList.add('text-[#1B4332]', 'border-b-2', 'border-[#1B4332]');
        }
    </script>
</x-app-layout>