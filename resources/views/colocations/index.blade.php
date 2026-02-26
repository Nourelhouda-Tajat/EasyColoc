<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes colocations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <!-- Bouton Créer -->
                    <div class="mb-6">
                        <a href="{{ route('colocations.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            + Créer une colocation
                        </a>
                    </div>

                    <!-- Liste -->
                    @forelse($colocations as $coloc)
                        <div class="border border-gray-200 rounded-lg p-4 mb-3 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('colocations.show', $coloc) }}" 
                                           class="hover:text-indigo-600">
                                            {{ $coloc->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Créée le {{ $coloc->created_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">
                            Vous n'avez pas encore de colocation active.
                            <br>
                            <a href="{{ route('colocations.create') }}" class="text-indigo-600 hover:underline">
                                Créez-en une maintenant !
                            </a>
                        </p>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</x-app-layout>