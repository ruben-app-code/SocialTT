@php
    $dashboardUser = $dashboardUser ?? auth()->user()->loadMissing(['socialAccounts.socialNetwork']);
    $publicCreatorUrl = $dashboardUser->creator_profile_url;
    $publicCreatorPath = parse_url($publicCreatorUrl, PHP_URL_PATH) ?: '/';
@endphp

<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Mi panel') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="flex items-start gap-4 min-w-0 flex-1">
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center text-violet-600 dark:text-violet-400">
                                <x-feather-icon name="user" class="w-6 h-6" />
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Datos personales') }}</h3>
                                <dl class="mt-3 space-y-2 text-sm">
                                    <div class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <dt class="text-gray-500 dark:text-gray-400 shrink-0">{{ __('Nombre') }}</dt>
                                        <dd class="text-gray-900 dark:text-white font-medium">{{ $dashboardUser->name }}</dd>
                                    </div>
                                    <div class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <dt class="text-gray-500 dark:text-gray-400 shrink-0">{{ __('Correo') }}</dt>
                                        <dd class="text-gray-900 dark:text-white break-all">{{ $dashboardUser->email }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                        <div class="min-w-0 w-full lg:max-w-xl lg:shrink-0 border-t lg:border-t-0 lg:border-l border-gray-200 dark:border-gray-600 pt-6 lg:pt-0 lg:pl-6">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Tu perfil público en este sitio') }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Enlace para compartir (como te ven los visitantes). Si tu TikTok está verificado, la ruta usa @; si no, /u/.') }}</p>
                            <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-2">
                                <code class="block flex-1 min-w-0 text-xs sm:text-sm px-3 py-2 rounded-lg bg-gray-100 text-gray-900 border border-gray-200 break-all dark:bg-slate-950 dark:text-slate-100 dark:border-slate-500/70 dark:shadow-inner">{{ $publicCreatorUrl }}</code>
                                <a href="{{ $publicCreatorUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 shrink-0 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                    <x-feather-icon name="external-link" class="w-4 h-4" />
                                    {{ __('Ver perfil público') }}
                                </a>
                            </div>
                            @if ($dashboardUser->role === 'creator' && str_starts_with($publicCreatorPath, '/creadores/'))
                                <p class="mt-2 text-xs text-amber-700 dark:text-amber-300">{{ __('Para una URL corta tipo dominio/@usuario o dominio/u/usuario, vincula tu cuenta de TikTok en Redes sociales.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if (auth()->user()->role === 'creator')
                <a href="{{ route('personal-links.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300">
                            <x-feather-icon name="link" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Enlaces personales') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Web, tienda u otras páginas en tu perfil') }}</p>
                        </div>
                    </div>
                </a>
                @endif

                <a href="{{ route('social-accounts.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <x-feather-icon name="link-2" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Redes sociales') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Gestiona tus cuentas vinculadas') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('schedules.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <x-feather-icon name="clock" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Horarios') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Configura tus horarios de publicación') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('polls.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <x-feather-icon name="bar-chart-2" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Encuestas') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Crea y gestiona encuestas') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('live-announcements.index') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center text-rose-600 dark:text-rose-400">
                            <x-feather-icon name="play-circle" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Próximos lives') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Anuncia tus transmisiones') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ url('/profile') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-sky-100 dark:bg-sky-900/40 flex items-center justify-center text-sky-600 dark:text-sky-400">
                            <x-feather-icon name="user" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Mi perfil') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Datos y configuración') }}</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('configuration.show') }}" class="block p-6 bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300">
                            <x-feather-icon name="settings" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Configuración') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Tema y preferencias') }}</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-creator-layout>
