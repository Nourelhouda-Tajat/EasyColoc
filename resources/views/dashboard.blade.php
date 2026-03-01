<x-app-layout>
    <x-alert />
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-4xl font-serif-custom text-[#1B4332]">Tableau de bord</h1>
            <p class="text-gray-400 mt-2 font-medium italic">Bonjour {{ Auth::user()->name }} </p>
        </div>
        
        @if(!Auth::user()->hasActiveColocation())
            <a href="{{ route('colocations.create') }}" class="bg-[#2D5A4C] text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 shadow-lg shadow-[#2D5A4C]/20 hover:bg-[#1B4332] transition">
                <span class="text-lg">+</span> Nouvelle colocation
            </a>
        @endif
    </div>

    @if($pendingInvitations->count() > 0)
        <div class="mb-10">
            <h2 class="text-xl font-serif-custom text-[#1B4332] mb-4 uppercase tracking-widest text-sm font-bold">{{ $pendingInvitations->count() }} Invitation(s) en attente</h2>
            @foreach($pendingInvitations as $invitation)
                <div class="bg-orange-50 border border-orange-100 p-4 rounded-2xl flex justify-between items-center mb-3">
                    <div class="flex items-center gap-4">
                        <span class="text-2xl">📩</span>
                        <div>
                            <p class="text-[#1B4332] font-bold">Invitation pour rejoindre "{{ $invitation->colocation->name }}"</p>
                            <p class="text-xs text-gray-500 font-medium">Envoyée par {{ $invitation->colocation->owner->name }}</p>
                        </div>
                    </div>
                    <a href="{{ route('invitations.show', $invitation->token) }}" class="bg-[#1B4332] text-white px-4 py-2 rounded-lg text-xs font-bold hover:opacity-90 transition">
                        Voir l'invitation
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Stats section  --}}
    <div class="grid grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-2xl shadow-sm">
            <p class="text-sm text-gray-500">Total dépenses</p>
            <p class="text-2xl font-bold text-[#1B4332]">{{ number_format($stats['total_expenses'], 2, ',', ' ') }} DH</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm">
            <p class="text-sm text-gray-500">Mon solde</p>
            <p class="text-2xl font-bold {{ $stats['user_balance'] < 0 ? 'text-red-500' : 'text-green-500' }}">
                {{ number_format($stats['user_balance'], 2, ',', ' ') }} DH
            </p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm">
            <p class="text-sm text-gray-500">Membres</p>
            <p class="text-2xl font-bold text-[#1B4332]">{{ $stats['members_count'] }}</p>
        </div>
    </div>

    <h2 class="text-2xl font-serif-custom text-[#1B4332] mb-6 leading-tight">Mes colocations</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        
        @if($activeMembership)
            <div class="bg-white p-8 rounded-[40px] border-2 border-[#1B4332] relative shadow-xl shadow-[#1B4332]/5">
                <span class="absolute top-6 right-6 bg-[#F0F4F2] text-[#1B4332] text-[10px] font-bold px-4 py-1 rounded-full uppercase tracking-tighter">Active</span>
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-16 h-16 bg-[#F9F8F3] rounded-[24px] flex items-center justify-center text-3xl shadow-inner">🏠</div>
                    <div>
                        {{-- accès au nom via la relation colocation --}}
                        <h4 class="text-2xl font-bold text-[#1B4332]">{{ $activeMembership->colocation->name }}</h4>
                        {{-- vérification du rôle via le champ 'role' de Membership --}}
                        <p class="text-[11px] font-bold text-orange-400 uppercase tracking-[0.2em] mt-1">
                            ★ {{ strtoupper($activeMembership->role) }}
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-[#F9F8F3] p-5 rounded-3xl text-center">
                        <p class="text-xl font-bold text-[#1B4332]">{{ $stats['members_count'] }}</p>
                        <p class="text-[9px] text-gray-400 uppercase font-bold tracking-widest mt-1">Membres dans la coloc</p>
                    </div>
                </div>
            </div>
        @endif

        @foreach($inactiveColocations as $membership)
            <div class="bg-gray-50 p-8 rounded-[40px] border border-gray-200 relative grayscale opacity-70">
                <span class="absolute top-6 right-6 bg-gray-200 text-gray-500 text-[10px] font-bold px-4 py-1 rounded-full uppercase tracking-tighter">Terminée</span>
                <div class="flex items-center gap-5 mb-10">
                    <div class="w-16 h-16 bg-white rounded-[24px] flex items-center justify-center text-3xl">🏠</div>
                    <div>
                        <h4 class="text-2xl font-bold text-gray-600">{{ $membership->colocation->name }}</h4>
                    </div>
                </div>
            </div>
        @endforeach

        @if(!$activeMembership)
            <a href="{{ route('colocations.create') }}" class="border-4 border-dashed border-gray-200 rounded-[40px] p-12 flex flex-col items-center justify-center group hover:bg-white/50 transition duration-300">
                <div class="w-12 h-12 flex items-center justify-center text-[#2D5A4C] text-4xl mb-4 group-hover:scale-110 transition">+</div>
                <p class="font-bold text-[#2D5A4C] text-lg">Créer une colocation</p>
            </a>
        @endif
    </div>
</x-app-layout>