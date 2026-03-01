<div>
    {{-- Message de succès --}}
    @if(session('success'))
        <div class="mb-6 bg-[#F0FDF4] border border-green-200 text-green-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <span class="text-xl"></span>
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Messages d'erreur --}}
    @if($errors->any())
        <div class="mb-6 bg-[#FEF2F2] border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <span class="text-xl"></span>
            <ul class="text-sm font-bold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>