@props(['reputation' => 0])

@if($reputation > 0)
    <span class="bg-green-50 text-green-600 text-[9px] font-bold px-3 py-1 rounded-lg uppercase border border-green-200">
        +{{ $reputation }} 
    </span>
@elseif($reputation < 0)
    <span class="bg-red-500 text-white text-[9px] font-extrabold px-3 py-1 rounded-lg uppercase shadow-md shadow-red-500/40 tracking-wider">
        -{{ $reputation }}
    </span>
@else
    <span class="bg-blue-50 text-blue-600 text-[9px] font-bold px-3 py-1 rounded-lg uppercase border border-blue-200">
        0 new
    </span>
@endif