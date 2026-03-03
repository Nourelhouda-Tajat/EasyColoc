<nav class="flex flex-col h-full py-8 text-white">
    <div class="px-8 mb-12">
        <div class="flex items-center gap-2">
            <x-application-logo class="w-8 h-8 fill-current text-white" />
            <span class="text-xl font-bold tracking-tight">EasyColoc</span>
        </div>
    </div>

    <div class="flex-1 px-4 space-y-10">
        <div>
            <p class="px-4 text-[10px] uppercase tracking-widest text-white/40 font-bold mb-4">Navigation</p>
            <div class="space-y-2">
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                    <span class="text-lg">🏠</span>
                    <span class="font-medium">Tableau de bord</span>
                </a>

                <a href="{{ route('colocations.index') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-2xl transition {{ request()->routeIs('colocations.*') ? 'bg-white/10 text-white' : 'text-white/60 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <span class="text-lg">🏢</span>
                        <span class="font-medium">Ma colocation</span>
                    </div>
                </a>
            </div>
        </div>

        <div>
            <p class="px-4 text-[10px] uppercase tracking-widest text-white/40 font-bold mb-4">Compte</p>
            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-white/60 hover:text-white transition">
                    <span class="text-lg">👤</span>
                    <span class="font-medium">Mon profil</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl text-[#E26D5C] hover:bg-red-500/10 transition">
                        <span class="text-lg">🚪</span>
                        <span class="font-medium text-left">Déconnexion</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="px-6 mt-auto">
        <div class="flex items-center gap-3 p-4 bg-black/10 rounded-3xl border border-white/5">
            <div class="w-10 h-10 bg-[#4B6B5F] rounded-full flex items-center justify-center font-bold text-xs">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-white/40 truncate italic">Owner · Le Nid Douillet</p>
            </div>
        </div>
    </div>
</nav>