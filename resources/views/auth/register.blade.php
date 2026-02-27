<x-guest-layout>
    <div class="w-full md:w-1/2 bg-[#2D5A4C] p-8 md:p-12 text-white flex flex-col justify-between">
        <div>
            <div class="flex items-center gap-2 mb-12">
                <div class="p-2 bg-white/20 rounded-lg">
                    <x-application-logo class="w-8 h-8 fill-current text-white" />
                </div>
                <span class="text-xl font-bold tracking-tight">EasyColoc</span>
            </div>

            <h1 class="text-4xl md:text-5xl font-serif font-bold leading-tight mb-6 italic">
                Rejoignez <br> votre coloc <br> en 30 sec.
            </h1>
            <p class="text-white/80 text-lg mb-10">
                Créez votre compte, rejoignez ou créez une colocation, et commencez à partager les dépenses.
            </p>

            <div class="space-y-4">
                <div class="bg-white/10 p-4 rounded-2xl flex items-center gap-4 border border-white/10 backdrop-blur-sm">
                    <div class="p-2 bg-white/20 rounded-xl text-xl">🔒</div>
                    <div>
                        <h3 class="font-bold uppercase text-xs tracking-widest text-white/90">100% sécurisé</h3>
                        <p class="text-sm text-white/60">Vos données sont chiffrées</p>
                    </div>
                </div>
                <div class="bg-white/10 p-4 rounded-2xl flex items-center gap-4 border border-white/10 backdrop-blur-sm">
                    <div class="p-2 bg-white/20 rounded-xl text-xl">📧</div>
                    <div>
                        <h3 class="font-bold uppercase text-xs tracking-widest text-white/90">Invitation par email</h3>
                        <p class="text-sm text-white/60">Invitez vos colocataires facilement</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full md:w-1/2 p-8 md:p-16 flex flex-col justify-center bg-[#F9F8F3]">
        <div class="max-w-md mx-auto w-full">
            <h2 class="text-4xl font-serif font-bold text-gray-800 mb-2">Créer un compte</h2>
            <p class="text-gray-500 mb-10 italic">Rejoignez des milliers de colocataires organisés</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-1">Nom complet</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#2D5A4C] focus:ring-[#2D5A4C] bg-white transition shadow-sm"
                        placeholder="Alice Dupont">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#2D5A4C] focus:ring-[#2D5A4C] bg-white transition shadow-sm"
                        placeholder="alice@exemple.fr">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Mot de passe</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#2D5A4C] focus:ring-[#2D5A4C] bg-white transition shadow-sm"
                        placeholder="Au moins 8 caractères">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required 
                        class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#2D5A4C] focus:ring-[#2D5A4C] bg-white transition shadow-sm"
                        placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="pt-4 text-center">
                    <button type="submit" class="w-full bg-[#2D5A4C] hover:bg-[#23473c] text-white font-bold py-4 rounded-xl transition duration-200 shadow-lg mb-6">
                        Créer mon compte
                    </button>
                    
                    <p class="text-sm text-gray-600">
                        Déjà un compte ? 
                        <a href="{{ route('login') }}" class="text-[#2D5A4C] font-bold hover:underline ml-1">
                            Se connecter
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>