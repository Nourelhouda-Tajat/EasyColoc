<x-guest-layout>
    <div class="w-full md:w-1/2 bg-[#2D5A4C] p-8 md:p-12 text-white flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 mb-12">
                <div class="p-2 bg-white/20 rounded-lg">
                    <x-application-logo class="w-8 h-8 fill-current text-white" />
                </div>
                <span class="text-xl font-bold tracking-tight">EasyColoc</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-serif font-bold leading-tight mb-6">
                La coloc, <br> sans les prises <br> de tête.
            </h1>
            <p class="text-white/80 text-lg mb-10">
                Suivez les dépenses communes et calculez automatiquement qui doit quoi à qui — en temps réel.
            </p>

            <div class="space-y-4">
                <div class="bg-white/10 p-4 rounded-2xl flex items-center gap-4 border border-white/10 backdrop-blur-sm">
                    <div class="p-2 bg-white/20 rounded-xl text-xl">📊</div>
                    <div>
                        <h3 class="font-bold">Tableau de bord intelligent</h3>
                        <p class="text-sm text-white/60">Visualisez vos soldes en un coup d'œil</p>
                    </div>
                </div>
                <div class="bg-white/10 p-4 rounded-2xl flex items-center gap-4 border border-white/10 backdrop-blur-sm">
                    <div class="p-2 bg-white/20 rounded-xl text-xl">⚡</div>
                    <div>
                        <h3 class="font-bold">Calcul automatique</h3>
                        <p class="text-sm text-white/60">Plus de calculs manuels, jamais</p>
                    </div>
                </div>
                <div class="bg-white/10 p-4 rounded-2xl flex items-center gap-4 border border-white/10 backdrop-blur-sm">
                    <div class="p-2 bg-white/20 rounded-xl text-xl">⭐</div>
                    <div>
                        <h3 class="font-bold">Système de réputation</h3>
                        <p class="text-sm text-white/60">Valorisez les bons payeurs</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col justify-center bg-white">
        <div class="max-w-md mx-auto w-full">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Bon retour !</h2>
            <p class="text-gray-500 mb-10">Connectez-vous à votre espace colocation</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-6">
                    <x-input-label for="email" value="Adresse email" class="text-gray-700 font-medium mb-2" />
                    <x-text-input id="email" class="block mt-1 w-full border-gray-200 focus:border-[#2D5A4C] focus:ring-[#2D5A4C] rounded-xl p-3" 
                        type="email" name="email" :value="old('email')" required autofocus 
                        placeholder="alice@coloc.fr" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mb-2">
                    <x-input-label for="password" value="Mot de passe" class="text-gray-700 font-medium mb-2" />
                    <x-text-input id="password" class="block mt-1 w-full border-gray-200 focus:border-[#2D5A4C] focus:ring-[#2D5A4C] rounded-xl p-3" 
                        type="password" name="password" required 
                        placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mb-8">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-[#2D5A4C] hover:underline font-medium" href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <button type="submit" class="w-full bg-[#2D5A4C] hover:bg-[#23473c] text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg shadow-green-900/20 uppercase tracking-widest text-sm">
                    Se connecter
                </button>
            </form>

            <p class="text-center mt-10 text-gray-600">
                Pas encore de compte ? 
                <a href="{{ route('register') }}" class="text-[#2D5A4C] font-bold hover:underline ml-1">
                    S'inscrire
                </a>
            </p>
        </div>
    </div>
</x-guest-layout>