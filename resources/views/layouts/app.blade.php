<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EasyColoc') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#F9F8F3]">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-[#1B4332] text-white flex flex-col fixed h-full">
            <div class="p-6">
                <div class="flex items-center gap-2 mb-8">
                    <x-application-logo class="w-8 h-8 fill-current text-white" />
                    <span class="text-xl font-bold tracking-tight">EasyColoc</span>
                </div>

                @include('layouts.navigation')
            </div>

            <div class="mt-auto p-6 border-t border-white/10 flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center font-bold text-xs">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-white/50 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </aside>

        <main class="flex-1 ml-64 p-8">
            @isset($header)
                <div class="flex justify-between items-center mb-8">
                    <div>
                        {{ $header }}
                    </div>
                    <div class="flex items-center gap-4">
                        </div>
                </div>
            @endisset

            {{ $slot }}
        </main>
    </div>
</body>
</html>