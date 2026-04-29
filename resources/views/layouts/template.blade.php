<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $appearanceIsDark ?? false])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#704FFE">

    <title>@yield('title', config('app.name'))</title>

    @auth
    @if ($appearanceUseSystemScript ?? false)
    <script>
    (function() {
        var t = @json(session('theme', 'system'));
        document.documentElement.classList.remove('dark');
        if (t === 'dark') document.documentElement.classList.add('dark');
        else if (t === 'light') {}
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,400&family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <style>
        .font-template { font-family: 'Nunito Sans', ui-sans-serif, sans-serif; }
        .font-template-title { font-family: 'Poppins', ui-sans-serif, sans-serif; }
        .bg-gradient-template { background: linear-gradient(169.71deg, #FFFBF7 0.8%, #FBE7DF 100.86%); }
        .dark .bg-gradient-template { background: linear-gradient(169.71deg, #1f2937 0.8%, #111827 100.86%); }
        .text-primary-template { color: #FE9063; }
        .bg-primary-template { background-color: #FE9063; }
        .text-secondary-template { color: #704FFE; }
        .bg-secondary-template { background-color: #704FFE; }
        .sidebar-transition { transition: transform 0.3s ease, opacity 0.3s ease; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="font-template antialiased bg-gradient-template min-h-screen text-gray-800 dark:text-gray-200">
    <x-banner />

    <div class="min-h-screen flex flex-col pb-20 md:pb-0">
        {{-- Header fijo (estilo template) --}}
        <header class="sticky top-0 z-30 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 h-14 flex items-center justify-between">
                <div class="flex items-center min-w-0">
                    <h1 class="font-template-title font-semibold text-lg text-gray-900 dark:text-white truncate">
                        @yield('header_title', __('Inicio'))
                    </h1>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @yield('header_actions')
                    <x-appearance-switch compact />
                    @auth
                    <button type="button" id="template-sidebar-toggle" class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 md:hidden" aria-label="{{ __('Menú') }}">
                        <x-feather-icon name="menu" class="w-6 h-6" />
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[#704FFE] dark:text-indigo-400 hover:underline">{{ __('Entrar') }}</a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-sm font-medium text-[#FE9063] dark:text-orange-400 hover:underline">{{ __('Registrarse') }}</a>
                    @endif
                    @endauth
                </div>
            </div>
        </header>

        {{-- Overlay del sidebar (móvil) --}}
        <div id="template-sidebar-overlay" class="fixed inset-0 bg-black/40 z-40 hidden sidebar-transition md:hidden" aria-hidden="true"></div>

        {{-- Contenedor: sidebar + main (en escritorio van en fila) --}}
        <div class="flex-1 flex flex-col md:flex-row max-w-4xl w-full mx-auto md:w-full">
        {{-- Sidebar (deslizable desde la derecha en móvil; columna fija en escritorio) --}}
        @auth
        <aside id="template-sidebar" class="fixed top-0 right-0 h-full w-72 max-w-[85vw] bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700 shadow-xl z-50 flex flex-col sidebar-transition transform translate-x-full md:translate-x-0 md:relative md:shadow-none md:border-r md:border-l-0 md:shrink-0 md:w-64" aria-label="{{ __('Menú principal') }}">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#FE9063] to-[#704FFE] flex items-center justify-center text-white font-template-title font-semibold text-lg">
                        {{ Str::limit(Auth::user()->name ?? 'U', 1) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Hola') }}</p>
                        <p class="font-template-title font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 overflow-y-auto py-4">
                <p class="px-4 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">{{ __('Menú') }}</p>
                <ul class="space-y-0.5">
                    <li><a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="home" class="w-5 h-5" /></span>{{ __('Inicio') }}</a></li>
                    <li><a href="{{ route('explore') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="search" class="w-5 h-5" /></span>{{ __('Explorar') }}</a></li>
                    <li><a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="grid" class="w-5 h-5" /></span>{{ __('Mi panel') }}</a></li>
                    <li><a href="{{ route('following.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="user-plus" class="w-5 h-5" /></span>{{ __('Siguiendo') }}</a></li>
                    <li><a href="{{ url('/profile') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="user" class="w-5 h-5" /></span>{{ __('Perfil') }}</a></li>
                    <li><a href="{{ route('configuration.show') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="settings" class="w-5 h-5" /></span>{{ __('Configuración') }}</a></li>
                    <li class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-700"><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800"><span class="text-gray-500"><x-feather-icon name="log-out" class="w-5 h-5" /></span>{{ __('Cerrar sesión') }}</a></li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </nav>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="font-template-title text-sm font-semibold text-gray-700 dark:text-gray-300">{{ config('app.name') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Versión 1.0') }}</p>
            </div>
        </aside>
        @endauth

        {{-- Contenido principal --}}
        <main class="flex-1 min-w-0 w-full px-4 py-6">
            @yield('content')
        </main>
        </div>
    </div>

    {{-- Barra inferior tipo app (móvil) --}}
    @auth
    <nav class="fixed bottom-0 left-0 right-0 z-30 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 md:hidden">
        <div class="max-w-2xl mx-auto flex items-center justify-around h-16">
            <a href="{{ url('/') }}" class="flex flex-col items-center justify-center gap-0.5 text-gray-500 dark:text-gray-400 hover:text-[#FE9063]"><x-feather-icon name="home" class="w-6 h-6" /><span class="text-xs">Inicio</span></a>
            <a href="{{ route('explore') }}" class="flex flex-col items-center justify-center gap-0.5 text-gray-500 dark:text-gray-400 hover:text-[#FE9063]"><x-feather-icon name="search" class="w-6 h-6" /><span class="text-xs">Explorar</span></a>
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-full bg-gradient-to-r from-[#FE9063] to-[#704FFE] p-3 text-white shadow-lg -mt-4"><x-feather-icon name="grid" class="w-6 h-6" /><span class="text-xs">Panel</span></a>
            <a href="{{ url('/profile') }}" class="flex flex-col items-center justify-center gap-0.5 text-gray-500 dark:text-gray-400 hover:text-[#FE9063]"><x-feather-icon name="user" class="w-6 h-6" /><span class="text-xs">Perfil</span></a>
        </div>
    </nav>
    @endauth

    @stack('modals')
    @livewireScripts

    <script>
    (function() {
        var sidebar = document.getElementById('template-sidebar');
        var overlay = document.getElementById('template-sidebar-overlay');
        var toggle = document.getElementById('template-sidebar-toggle');
        if (!sidebar || !overlay) return;
        function openSidebar() {
            sidebar.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            sidebar.classList.add('translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }
        if (toggle) toggle.addEventListener('click', function() { sidebar.classList.contains('translate-x-full') ? openSidebar() : closeSidebar(); });
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSidebar(); });
    })();
    </script>
    @stack('scripts')
</body>
</html>
