<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $appearanceIsDark ?? false])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2196f3">

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
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,400&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <style>
        .font-template { font-family: 'Nunito Sans', ui-sans-serif, sans-serif; }
        .font-template-title { font-family: 'Poppins', ui-sans-serif, sans-serif; }
        .bg-gradient-profile-app { background: linear-gradient(165deg, #e3f2fd 0%, #f5f5f5 45%, #eceff1 100%); }
        .dark .bg-gradient-profile-app { background: linear-gradient(165deg, #0f172a 0%, #1e293b 50%, #111827 100%); }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="font-template antialiased bg-gradient-profile-app min-h-screen text-gray-800 dark:text-gray-200">
    <x-banner />

    <div class="page-wraper min-h-screen flex flex-col pb-20 md:pb-8">
        @include('creator.partials.profile-app-header', [
            'headerTitle' => trim($__env->yieldContent('header_title', __('Perfil'))),
        ])

        <div class="page-content flex-1 w-full max-w-2xl mx-auto px-4 pt-[calc(3.5rem+env(safe-area-inset-top))] pb-4">
            @yield('content')
        </div>

        @auth
            @include('creator.partials.profile-app-footer')
        @endauth
    </div>

    @stack('modals')
    @livewireScripts
    @stack('scripts')
</body>
</html>
