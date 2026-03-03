<aside class="w-64 bg-[#1B4332] text-white flex flex-col fixed h-full py-6">
    <div class="px-6 mb-10 flex items-center gap-2">
        <div class="p-2 bg-white/10 rounded-lg">🏠</div>
        <span class="text-xl font-bold tracking-tight">EasyColoc</span>
    </div>

    <nav class="flex-1 px-4">
        {{-- Navigation Principale --}}
        <p class="px-4 text-[10px] uppercase tracking-widest text-white/40 font-bold mb-4">Navigation</p>
        <div class="space-y-2">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : 'text-white/60 hover:bg-white/5 transition' }}">
                <span>Tableau de bord</span>
            </a>
            <a href="{{ route('colocations.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('colocations.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:bg-white/5 transition' }}">
                <span>Mes colocation</span>
            </a>
        </div>

        {{-- Section Administration (VISIBLE UNIQUEMENT PAR LES ADMINS) --}}
        @if(Auth::check() && Auth::user()->is_admin)
            <p class="px-4 text-[10px] uppercase tracking-widest text-white/40 font-bold mt-10 mb-4">Administration</p>
            <div class="space-y-2">
                <a href="{{ route('admin.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('colocations.*') ? 'bg-white/15 text-white' : 'text-white/60 hover:bg-white/5 transition' }}">
                    <span>Panel Admin</span>
                </a>
            </div>
        @endif

        {{-- Compte Utilisateur --}}
        <p class="px-4 text-[10px] uppercase tracking-widest text-white/40 font-bold mt-10 mb-4">Compte</p>
        <div class="space-y-2">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('profile.edit') ? 'bg-white/15 text-white' : 'text-white/60 hover:bg-white/5 transition' }}">
                <span>Mon profil</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-[#E26D5C] hover:bg-red-500/10 transition">
                    <span class="text-left">Déconnexion</span>
                </button>
            </form>
        </div>
    </nav>

    <div class="px-4 mt-auto">
        <div class="flex items-center gap-3 p-3 bg-black/10 rounded-2xl border border-white/5">
            <div class="w-10 h-10 bg-[#4B6B5F] rounded-full flex items-center justify-center font-bold text-xs uppercase">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="truncate">
                <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-white/40 italic truncate">
                    {{ Auth::user()->is_admin ? ' Admin' : 'Utilisateur' }}
                </p>
            </div>
        </div>
    </div>
</aside>