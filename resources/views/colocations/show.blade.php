<x-app-layout>
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
            <form action="{{ route('colocations.destroy', $colocation) }}" method="POST" onsubmit="return confirm('Annuler cette coloc ?')">
                @csrf @method('DELETE')
                <button class="px-5 py-2.5 rounded-xl border border-red-100 text-red-500 font-bold text-sm hover:bg-red-50 transition">Annuler la coloc</button>
            </form>
            <button class="bg-[#2D5A4C] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-[#2D5A4C]/20 hover:bg-[#1B4332] transition">
                + Ajouter dépense
            </button>
        </div>
    </div>

    <div class="bg-[#2D5A4C] rounded-[32px] p-8 text-white mb-10 flex justify-between items-center shadow-xl shadow-[#2D5A4C]/10">
        <div>
            <p class="text-white/60 text-xs font-bold uppercase tracking-widest mb-2">Mon solde actuel</p>
            <h2 class="text-5xl font-bold">-87,50 €</h2>
            <p class="text-white/40 text-xs mt-4 italic font-medium">Vous devez de l'argent à vos colocataires</p>
        </div>
        <div class="text-right">
            <div class="bg-white/10 px-4 py-1.5 rounded-full text-[10px] font-bold inline-block mb-4">Janvier 2026</div>
            <p class="text-white/40 text-[10px] font-bold uppercase">Total dépenses</p>
            <p class="text-2xl font-bold">1 248,00 €</p>
        </div>
    </div>

    <div class="flex gap-8 border-b border-gray-100 mb-8 px-2">
        <button class="pb-4 text-[#1B4332] font-bold text-sm border-b-2 border-[#1B4332] flex items-center gap-2">
            <span>👥</span> Membres ({{ $activeMembers->count() }})
        </button>
        <button class="pb-4 text-gray-400 font-bold text-sm hover:text-[#1B4332] transition flex items-center gap-2">
            <span>💰</span> Dépenses (12)
        </button>
        <button class="pb-4 text-gray-400 font-bold text-sm hover:text-[#1B4332] transition flex items-center gap-2">
            <span>⚖️</span> Qui doit à qui
        </button>
    </div>

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
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>