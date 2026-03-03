{{-- Le fond grisé (caché par défaut) --}}
<div id="edit-expense-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center backdrop-blur-sm">
    
    {{-- La boite de dialogue --}}
    <div class="bg-white p-8 rounded-[32px] w-full max-w-lg shadow-2xl relative">
        
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-serif-custom text-[#1B4332]">Modifier la dépense</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold transition">✕</button>
        </div>

        {{-- Le formulaire dont l'action sera injectée en JS --}}
        <form id="edit-expense-form" method="POST" class="grid grid-cols-2 gap-4">
            @csrf
            @method('PUT')
            
            <input type="text" id="edit-title" name="title" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            
            <input type="number" step="0.01" id="edit-amount" name="amount" required class="bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            
            <input type="date" id="edit-date" name="date" required class="bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
            
            <select id="edit-category" name="category_id" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
            <select id="edit-payer" name="payer_id" required class="col-span-2 bg-[#F9F8F3] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#2D5A4C]">
                @foreach($activeMembers as $member)
                    <option value="{{ $member->user_id }}">{{ $member->user->name }}</option>
                @endforeach
            </select>
            
            <div class="col-span-2 flex gap-3 mt-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition">Annuler</button>
                <button type="submit" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition">Mettre à jour</button>
            </div>
        </form>
        
        {{-- Bouton de suppression séparé --}}
        <form id="delete-expense-form" method="POST" class="mt-4" onsubmit="return confirm('Supprimer définitivement cette dépense ?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full text-center text-red-400 text-xs font-bold hover:text-red-600 uppercase tracking-widest">Supprimer cette dépense</button>
        </form>
    </div>
</div>