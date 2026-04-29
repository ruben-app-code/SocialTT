<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $appearanceIsDark ?? false])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col font-sans antialiased">
        <header class="w-full border-b border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] shrink-0">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="font-semibold text-lg text-[#1b1b18] dark:text-[#EDEDEC]">{{ config('app.name') }}</a>
                <form action="{{ route('explore') }}" method="GET" class="flex-1 min-w-0 max-w-md order-last sm:order-none w-full sm:w-auto">
                    <div class="flex gap-2">
                        @if(request()->filled('topic'))
                            <input type="hidden" name="topic" value="{{ request('topic') }}">
                        @endif
                        <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Buscar creadores...') }}" class="flex-1 min-w-0 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] px-3 py-1.5 text-sm text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] focus:ring-1 focus:ring-[#F53003] dark:focus:ring-[#FF4433] focus:border-transparent">
                        <button type="submit" class="shrink-0 p-1.5 rounded-lg bg-[#e3e3e0] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#d0d0ce] dark:hover:bg-gray-600" aria-label="{{ __('Buscar') }}">
                            <x-feather-icon name="search" class="w-5 h-5" />
                        </button>
                    </div>
                </form>
                <nav class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <x-appearance-switch variant="guest" />
                    <a href="{{ route('explore') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] {{ request()->routeIs('explore') ? 'border-[#19140035] dark:border-[#3E3E3A]' : '' }}">{{ __('Explorar') }}</a>
                    <a href="{{ route('public.topics') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A]">{{ __('Temas') }}</a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A]">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A]">{{ __('Entrar') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b]">{{ __('Registrarse') }}</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <footer class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] py-4 shrink-0">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ config('app.name') }} — {{ __('Conecta con creadores') }}
            </div>
        </footer>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
