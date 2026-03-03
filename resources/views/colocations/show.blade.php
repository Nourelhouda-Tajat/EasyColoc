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
            @if(Auth::id() === $colocation->owner_id)
                <form action="{{ route('colocations.destroy', $colocation) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler toute la colocation ?')">
                    @csrf @method('DELETE')
                    <button class="px-5 py-2.5 rounded-xl border border-red-100 text-red-500 font-bold text-sm hover:bg-red-50 transition">Annuler la coloc</button>
                </form>
            @else
                <form action="{{ route('colocations.leave', $colocation) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment quitter cette colocation ?')">
                    @csrf
                    <button class="px-5 py-2.5 rounded-xl border border-orange-100 text-orange-500 font-bold text-sm hover:bg-orange-50 transition">Quitter la coloc</button>
                </form>
            @endif

            <button onclick="document.getElementById('add-expense-form').classList.toggle('hidden')" class="bg-[#2D5A4C] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#2D5A4C]/20 hover:bg-[#1B4332] transition">
                + Ajouter dépense
            </button>
        </div>
    </div>

    <div id="add-expense-form" class="hidden bg-white p-6 rounded-3xl mb-8 border border-gray-100">
        <h3 class="text-lg font-bold text-[#1B4332] mb-4">Ajouter une dépense</h3>
        <form action="{{ route('colocations.expenses.store', $colocation) }}" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            <input type="text" name="title" placeholder="Titre de la dépense" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            <input type="number" step="0.01" name="amount" placeholder="Montant (DH)" required class="bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
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
            <h2 class="text-5xl font-bold">{{ number_format($userBalance ?? 0, 2, ',', ' ') }} DH</h2>
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
            <p class="text-white/40 text-[10px] font-bold uppercase">Total dépenses</p>
            <p class="text-2xl font-bold">{{ number_format($totalExpenses ?? 0, 2, ',', ' ') }} DH</p>
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
                    <span>
                        <x-reputation-badge :reputation="$membership->user->reputation" />
                    </span>
                    
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
        
        <div class="mb-6">
            {{-- Select avec les 12 mois. Le onChange redirige avec l'URL + l'ancre #expenses --}}
            <select onchange="window.location.href=this.value + '#expenses'" class="bg-[#F0F4F2] text-[#1B4332] rounded-xl text-xs font-bold border-none px-4 py-2.5 focus:ring-2 focus:ring-[#2D5A4C] cursor-pointer outline-none">
                <option value="{{ route('colocations.show', $colocation) }}">Tous les mois</option>
                @foreach($filterMonths as $m)
                    <option value="{{ route('colocations.show', ['colocation' => $colocation, 'month' => $m['num'], 'year' => $selectedYear]) }}" {{ $selectedMonth == $m['num'] ? 'selected' : '' }}>
                        {{ $m['name'] }} {{ $selectedYear }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="space-y-3 mb-8">
            @forelse($expenses as $expense)
            <div class="bg-white p-5 rounded-2xl border border-gray-50 shadow-sm flex items-center justify-between group hover:border-[#1B4332]/20 transition">
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 rounded-full {{ $expense->is_payer ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                    <div>
                        <h4 class="font-bold text-[#1B4332]">{{ $expense->title }}</h4>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($expense->date)->translatedFormat('d M Y') }} · Payé par {{ $expense->payer->name }}</p>
                    </div>
                </div>
                
                <div class="text-right">
                    <p class="font-bold text-[#1B4332]">{{ number_format($expense->amount, 2, ',', ' ') }} DH</p>
                    {{-- TEXTE SUPPRIMÉ ICI SELON TA DEMANDE --}}
                </div>
                
                <div class="flex items-center gap-2">
                    <span class="bg-[#F9F8F3] text-gray-500 text-[9px] font-bold px-2 py-1 rounded uppercase mr-2">
                        {{ $expense->category->name }}
                    </span>
                    
                    {{-- Icônes Éditer et Supprimer : Visibles au hover par l'Owner ou le Payeur --}}
                    @if(Auth::id() === $colocation->owner_id || $expense->is_payer)
                        <button type="button" 
                            onclick="openEditModal({{ $expense->id }}, '{{ addslashes($expense->title) }}', {{ $expense->amount }}, '{{ $expense->date }}', {{ $expense->category_id }}, {{ $expense->payer_id }})" 
                            class="flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold hover:bg-blue-100 transition" title="Modifier">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>

                        <form action="{{ route('colocations.expenses.destroy', ['colocation' => $colocation->id, 'expense' => $expense->id]) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer définitivement cette dépense ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-[10px] font-bold hover:bg-red-100 transition" title="Supprimer">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
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
            @forelse($suggestedSettlements as $settlement)
                @php
                    $isUserDebtor = $settlement['debtor_id'] === Auth::id();
                    $isUserCreditor = $settlement['creditor_id'] === Auth::id();
                    $borderColor = $isUserDebtor ? 'border-red-400' : ($isUserCreditor ? 'border-green-400' : 'border-gray-200');
                @endphp

                <div class="bg-white p-5 rounded-2xl border-l-4 {{ $borderColor }} shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 bg-[#F9F8F3] rounded-full flex items-center justify-center font-bold text-xs border-2 border-white text-[#1B4332]" title="{{ $settlement['debtor']->name }}">
                                {{ strtoupper(substr($settlement['debtor']->name, 0, 2)) }}
                            </div>
                            <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center font-bold text-xs text-white border-2 border-white" title="{{ $settlement['creditor']->name }}">
                                {{ strtoupper(substr($settlement['creditor']->name, 0, 2)) }}
                            </div>
                        </div>
                        <div>
                            @if($isUserDebtor)
                                <h4 class="font-bold text-[#1B4332]">Vous devez à {{ $settlement['creditor']->name }}</h4>
                            @elseif($isUserCreditor)
                                <h4 class="font-bold text-[#1B4332]">{{ $settlement['debtor']->name }} vous doit</h4>
                            @else
                                <h4 class="font-bold text-[#1B4332]">{{ $settlement['debtor']->name }} doit à {{ $settlement['creditor']->name }}</h4>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <span class="text-xl font-bold {{ $isUserCreditor ? 'text-green-600' : ($isUserDebtor ? 'text-red-500' : 'text-gray-600') }}">
                            {{ number_format($settlement['amount'], 2, ',', ' ') }} DH
                        </span>
                        
                        @if($isUserDebtor)
                            <form action="{{ route('colocations.settlements.store', $colocation) }}" method="POST">
                                @csrf
                                <input type="hidden" name="creditor_id" value="{{ $settlement['creditor_id'] }}">
                                <input type="hidden" name="amount" value="{{ $settlement['amount'] }}">
                                <button type="submit" onclick="return confirm('Confirmer que vous avez remboursé {{ $settlement['amount'] }} DH à {{ $settlement['creditor']->name }} ?')" class="bg-green-50 text-green-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-green-100 transition">
                                    Marquer payé
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <p class="text-4xl mb-2">🎉</p>
                    <p>Tout est équilibré ! Personne ne doit rien à personne.</p>
                </div>
            @endforelse
        </div>
    </div>

    @include('colocations.partials.edit-expense-modal')

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
            window.history.replaceState(null, null, '#' + tabName);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var hash = window.location.hash.substr(1);
            if(hash === 'expenses' || hash === 'members' || hash === 'debts') {
                showTab(hash);
            } 
            else if (window.location.search.includes('month=')) {
                showTab('expenses');
            }
        });
        
        function openEditModal(expenseId, title, amount, date, categoryId, payerId) {
            document.getElementById('edit-expense-modal').classList.remove('hidden');
            
            let updateUrl = "{{ route('colocations.expenses.update', ['colocation' => $colocation->id, 'expense' => 'EXPENSE_ID']) }}";
            let deleteUrl = "{{ route('colocations.expenses.destroy', ['colocation' => $colocation->id, 'expense' => 'EXPENSE_ID']) }}";
            
            document.getElementById('edit-expense-form').action = updateUrl.replace('EXPENSE_ID', expenseId);
            document.getElementById('delete-expense-form').action = deleteUrl.replace('EXPENSE_ID', expenseId);

            document.getElementById('edit-title').value = title;
            document.getElementById('edit-amount').value = amount;
            document.getElementById('edit-date').value = date;
            document.getElementById('edit-category').value = categoryId;
            document.getElementById('edit-payer').value = payerId;
        }

        function closeEditModal() {
            document.getElementById('edit-expense-modal').classList.add('hidden');
        }
    </script>
</x-app-layout>