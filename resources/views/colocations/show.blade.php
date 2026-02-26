<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Infos principales -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Créée par</p>
                        <p class="font-medium">{{ $colocation->owner->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Statut</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $colocation->isActive() ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $colocation->isActive() ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Membres -->
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Membres ({{ $colocation->activeMembers->count() }})</h3>
                
                <ul class="divide-y divide-gray-200">
                    @foreach($colocation->activeMembers as $membership)
                        <li class="py-3 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $membership->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $membership->user->email }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $membership->isOwner() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $membership->isOwner() ? 'Owner' : ' Membre' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Actions (Owner only) -->
            @if($colocation->owner_id === Auth::id())
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                    <div class="flex gap-3">
                        <a href="{{ route('colocations.edit', $colocation) }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('colocations.destroy', $colocation) }}" 
                              class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette colocation ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                                Annuler
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>