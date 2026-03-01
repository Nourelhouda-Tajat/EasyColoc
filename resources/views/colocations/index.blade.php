<x-app-layout>
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-serif-custom text-[#1B4332]">Mes colocations</h1>
            <p class="text-gray-500">Gérez vos espaces de colocation</p>
        </div>
        <a href="{{ route('colocations.create') }}" class="flex items-center gap-2 text-[#1B4332] font-bold text-sm">
            <span class="text-xl">+</span> Créer une colocation
        </a>
    </div>

    <div class="space-y-6">
        @forelse($colocations as $membership)
            <div class="bg-white p-6 rounded-3xl shadow-sm flex items-center justify-between border border-transparent hover:border-[#1B4332] transition">
                <div class="flex items-center gap-6">
                    <div class="text-3xl">🏠</div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $membership->role }}</p>
                        <h3 class="text-xl font-bold text-[#1B4332]">{{ $membership->colocation->name }}</h3>
                    </div>
                </div>
                
                <div class="flex gap-12 text-center">
                    <div>
                        <p class="text-lg font-bold">4</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Membres</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold">0 DH</p>
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Dépenses</p>
                    </div>
                </div>

                <a href="{{ route('colocations.show', $membership->colocation) }}" class="bg-[#F9F8F3] p-3 rounded-xl hover:bg-[#1B4332] hover:text-white transition">
                    Voir →
                </a>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-[40px] border-4 border-dashed border-gray-100">
                <p class="text-gray-400 font-medium text-lg">Aucune colocation active</p>
            </div>
        @endforelse
    </div>
</x-app-layout>