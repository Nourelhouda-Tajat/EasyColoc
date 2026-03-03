<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>EasyColoc</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
        <style>
            .font-serif-custom { font-family: 'DM+Serif+Display', serif; }
        </style>
    </head>
    <body class="antialiased bg-[#F9F8F3]">
        <div class="flex min-h-screen">
            @include('layouts.navigation')

            <main class="flex-1 ml-64 p-10">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>