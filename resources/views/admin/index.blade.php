<x-app-layout>
    <x-alert />
    
    <div class="mb-8">
        <h1 class="text-4xl font-serif-custom text-[#1B4332]">Administration Globale</h1>
        <p class="text-gray-400 mt-2 font-medium italic">Vue d'ensemble et gestion des utilisateurs</p>
    </div>

    {{-- STATISTIQUES --}}
    <div class="grid grid-cols-3 gap-6 mb-12">
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-[#1B4332]">
            <p class="text-sm text-gray-500 font-bold uppercase">Utilisateurs</p>
            <p class="text-3xl font-bold text-[#1B4332] mt-2">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-red-500 mt-1">Dont {{ $stats['banned_users'] }} bannis</p>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-[#2D5A4C]">
            <p class="text-sm text-gray-500 font-bold uppercase">Colocations</p>
            <p class="text-3xl font-bold text-[#1B4332] mt-2">{{ $stats['total_colocations'] }}</p>
            <p class="text-xs text-green-500 mt-1">{{ $stats['active_colocations'] }} actives · <span class="text-gray-400">{{ $stats['inactive_colocations'] }} terminées</span></p>
        </div>

        <div class="bg-[#1B4332] p-6 rounded-2xl shadow-sm">
            <p class="text-sm text-white/60 font-bold uppercase">Argent géré (Global)</p>
            <p class="text-3xl font-bold text-white mt-2">{{ number_format($stats['total_expenses'], 2, ',', ' ') }} DH</p>
        </div>
    </div>

    {{-- GESTION DES UTILISATEURS --}}
    <h2 class="text-2xl font-serif-custom text-[#1B4332] mb-6">Gestion des utilisateurs</h2>
    
    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-[#F9F8F3] text-[#1B4332] text-xs uppercase tracking-widest">
                    <th class="p-4 font-bold">Utilisateur</th>
                    <th class="p-4 font-bold">Email</th>
                    <th class="p-4 font-bold text-center">Réputation</th>
                    <th class="p-4 font-bold text-center">Statut</th>
                    <th class="p-4 font-bold text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-[#1B4332] flex items-center gap-3">
                            <div class="w-8 h-8 bg-[#F0F4F2] rounded-full flex items-center justify-center text-xs">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            {{ $user->name }}
                        </td>
                        <td class="p-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="p-4 text-center">
                            <x-reputation-badge :reputation="$user->reputation" />
                        </td>
                        <td class="p-4 text-center">
                            @if($user->is_banned)
                                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded">Banni</span>
                            @else
                                <span class="bg-green-100 text-green-600 text-xs font-bold px-2 py-1 rounded">Actif</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <form action="{{ route('admin.users.toggle-ban', $user) }}" method="POST">
                                @csrf
                                @if($user->is_banned)
                                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                        Débannir
                                    </button>
                                @else
                                    <button type="submit" onsubmit="return confirm('Voulez-vous vraiment bannir cet utilisateur ?')" class="bg-red-50 hover:bg-red-500 hover:text-white text-red-500 px-3 py-1.5 rounded-lg text-xs font-bold border border-red-100 transition">
                                        Bannir
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-gray-50 bg-[#F9F8F3]">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>