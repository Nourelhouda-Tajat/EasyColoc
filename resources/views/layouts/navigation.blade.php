<nav class="space-y-2">
    <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold mb-4">Navigation</p>
    
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-white/10">
        <span class="text-lg">🏠</span>
        <span>Tableau de bord</span>
    </x-nav-link>

    <x-nav-link :href="route('colocations.index')" :active="request()->routeIs('colocations.*')" class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-white/10">
        <span class="text-lg">🏢</span>
        <span>Ma colocation</span>
    </x-nav-link>

    <div class="pt-8">
        <p class="text-[10px] uppercase tracking-widest text-white/40 font-bold mb-4">Compte</p>
        
        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-white/10">
            <span class="text-lg">👤</span>
            <span>Mon profil</span>
        </x-nav-link>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 p-3 rounded-xl transition hover:bg-red-500/20 text-red-200 mt-2">
                <span class="text-lg">🚪</span>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>
</nav>