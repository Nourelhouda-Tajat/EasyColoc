<x-guest-layout>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Mot de passe oublié ?</h2>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Vous avez oublié votre mot de passe ? Indiquez votre adresse email et nous vous enverrons un lien pour en choisir un nouveau.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Adresse email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Envoyer le lien') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>