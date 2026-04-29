<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $appearanceIsDark ?? false])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
        @if ($appearanceUseSystemScript ?? false)
        <script>
        (function() {
            var t = @json(session('theme', 'system'));
            document.documentElement.classList.remove('dark');
            if (t === 'dark') document.documentElement.classList.add('dark');
            else if (t === 'light') { }
            else {
                if (window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark');
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                    document.documentElement.classList.toggle('dark', e.matches);
                });
            }
        })();
        </script>
        @endif
        @endauth
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased flex flex-col min-h-screen">
        <x-banner />
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col flex-1">
            @include('layouts.administrator.header')
            @include('layouts.administrator.menu')
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <main class="flex-1">
                {{ $slot }}
            </main>
            @include('layouts.administrator.footer')
        </div>
        @stack('modals')
        @stack('scripts')
        @livewireScripts
    </body>
</html>
