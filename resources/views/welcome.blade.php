<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => $appearanceIsDark ?? false])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License */@layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif;}--color-red-500:oklch(.637 .237 25.331);--color-gray-50:oklch(.985 .002 247.839);--color-gray-100:oklch(.967 .003 264.542);--color-gray-200:oklch(.928 .006 264.531);--color-gray-500:oklch(.551 .027 264.364);--color-gray-700:oklch(.373 .034 259.733);--color-gray-800:oklch(.278 .033 256.848);--color-gray-900:oklch(.21 .034 264.665);--color-indigo-600:oklch(.546 .245 262.881);--color-white:#fff;--color-black:#000;}@layer base{*,:after,:before{box-sizing:border-box;margin:0;padding:0}html{line-height:1.5;font-family:var(--font-sans)}body{line-height:inherit}a{color:inherit;text-decoration:inherit}img{max-width:100%;height:auto;display:block}}@layer utilities{.min-h-screen{min-height:100vh}.w-full{width:100%}.max-w-7xl{max-width:80rem}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-2{gap:.5rem}.gap-4{gap:1rem}.gap-6{gap:1.5rem}.gap-8{gap:2rem}.rounded-full{border-radius:9999px}.rounded-xl{border-radius:.75rem}.object-cover{object-fit:cover}.p-4{padding:1rem}.p-6{padding:1.5rem}.px-4{padding-left:1rem;padding-right:1rem}.px-5{padding-left:1.25rem;padding-right:1.25rem}.py-1\.5{padding-top:.375rem;padding-bottom:.375rem}.py-8{padding-top:2rem;padding-bottom:2rem}.mb-2{margin-bottom:.5rem}.mb-4{margin-bottom:1rem}.mb-6{margin-bottom:1.5rem}.mt-8{margin-top:2rem}.text-sm{font-size:.875rem}.text-2xl{font-size:1.5rem}.text-xl{font-size:1.25rem}.font-medium{font-weight:500}.font-semibold{font-weight:600}.font-bold{font-weight:700}.text-gray-500{color:var(--color-gray-500)}.text-gray-700{color:var(--color-gray-700)}.text-gray-900{color:var(--color-gray-900)}.text-white{color:var(--color-white)}.bg-white{background-color:var(--color-white)}.bg-gray-50{background-color:var(--color-gray-50)}.bg-gray-100{background-color:var(--color-gray-100)}.border{border-width:1px}.border-transparent{border-color:transparent}.border-gray-200{border-color:var(--color-gray-200)}.shadow{box-shadow:0 1px 3px 0 rgb(0 0 0 / 0.1)}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}.flex-col{flex-direction:column}.truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.hover\:border-gray-300:hover{border-color:var(--color-gray-200)}.hover\:shadow-lg:hover{box-shadow:0 10px 15px -3px rgb(0 0 0 / 0.1)}.sm\:grid-cols-2{@media (min-width:40rem){grid-template-columns:repeat(2,minmax(0,1fr))}}.lg\:grid-cols-4{@media (min-width:64rem){grid-template-columns:repeat(4,minmax(0,1fr))}}.lg\:px-8{@media (min-width:64rem){padding-left:2rem;padding-right:2rem}}.dark .dark\:bg-gray-800{background-color:var(--color-gray-800)}.dark .dark\:bg-gray-900{background-color:var(--color-gray-900)}.dark .dark\:text-gray-300{color:var(--color-gray-200)}.dark .dark\:text-gray-400{color:var(--color-gray-500)}.dark .dark\:text-white{color:var(--color-white)}.dark .dark\:border-gray-700{border-color:var(--color-gray-700)}}
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col">
        <header class="w-full border-b border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615]">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <a href="{{ route('home') }}" class="font-semibold text-lg text-[#1b1b18] dark:text-[#EDEDEC]">{{ config('app.name') }}</a>
                <nav class="flex flex-wrap items-center gap-3 sm:gap-4">
                    <x-appearance-switch variant="guest" />

                    <a href="{{ route('creadores') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A]">
                        {{ __('Creadores') }}
                    </a>

                       <a href="{{ route('explore') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A]">
                        {{ __('Explorar*') }}
                    </a>


                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A]">{{ __('Dashboard') }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline-flex items-center m-0">
                            @csrf
                            <button type="submit" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] cursor-pointer bg-transparent">
                                {{ __('Cerrar sesión') }}
                            </button>
                        </form>
                    @else

                        <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A]">{{ __('Entrar') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 rounded-sm text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b]">
                                {{ __('Registrarse') }}
                            </a>
                        @endif

                    @endauth
                </nav>
            </div>
        </header>

        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
            {{-- Hero --}}
            <section class="text-center mb-12 lg:mb-16">
                <h1 class="text-3xl sm:text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ config('app.name') }}</h1>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-lg max-w-2xl mx-auto mb-6">
                    {{ __('Descubre creadores, sigue tus favoritos y conecta con la comunidad.') }}
                </p>
                <a href="{{ route('explore') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-medium bg-[#F53003] dark:bg-[#FF4433] text-white hover:opacity-90 transition-opacity">
                    {{ __('Explorar creadores') }}
                    <x-feather-icon name="arrow-right" class="w-5 h-5" />
                </a>
            </section>

            {{-- Creadores destacados --}}
            <section>
                <h2 class="text-xl font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-6">{{ __('Creadores destacados') }}</h2>
                @if (isset($featuredCreators) && $featuredCreators->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($featuredCreators as $creator)
                            <a href="{{ $creator->creator_profile_url }}" class="flex flex-col items-center p-6 bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] shadow-sm hover:shadow-md transition-shadow">
                                <img src="{{ $creator->avatar_url }}" alt="" class="w-20 h-20 rounded-full object-cover mb-3 border-2 border-[#e3e3e0] dark:border-[#3E3E3A]" />
                                <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] truncate w-full text-center">{{ $creator->name }}</span>
                                <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $creator->social_accounts_count }} {{ __('redes') }}</span>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-8 text-center">
                        <a href="{{ route('explore') }}" class="inline-flex items-center gap-2 text-[#F53003] dark:text-[#FF4433] font-medium hover:underline">
                            {{ __('Ver todos los creadores') }}
                            <x-feather-icon name="chevron-right" class="w-5 h-5" />
                        </a>
                    </div>
                @else
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ __('Aún no hay creadores. Regístrate y sé el primero.') }}</p>
                    @if (!auth()->check() && Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-block px-5 py-2 rounded-lg font-medium border border-[#19140035] dark:border-[#3E3E3A]">{{ __('Registrarse') }}</a>
                    @endif
                @endif
            </section>
        </main>

        <footer class="border-t border-[#e3e3e0] dark:border-[#3E3E3A] py-4 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                {{ config('app.name') }} — {{ __('Conecta con creadores') }}
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
