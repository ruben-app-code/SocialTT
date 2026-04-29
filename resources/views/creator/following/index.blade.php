<x-creator-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Siguiendo') }}
            </h2>
            <a href="{{ route('following.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500 shrink-0">
                <x-feather-icon name="plus-circle" class="w-4 h-4" />
                {{ __('Añadir cuenta TikTok') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 border border-emerald-200 dark:border-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-900 dark:text-amber-100 border border-amber-200 dark:border-amber-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->has('creator'))
                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 border border-red-200 dark:border-red-800 text-sm">
                    {{ $errors->first('creator') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Buscar creadores en la plataforma') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Escribe al menos 2 caracteres (nombre, correo o usuario de una red vinculada). No se añade nada hasta que pulses «Añadir».') }}</p>
                <form method="get" action="{{ route('following.index') }}" class="flex flex-col sm:flex-row gap-2">
                    <input type="search" name="q" value="{{ $searchQuery }}" minlength="2" maxlength="120" placeholder="{{ __('Buscar…') }}"
                        class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-800 dark:bg-gray-600 text-white text-sm font-medium hover:bg-gray-700 dark:hover:bg-gray-500">
                        {{ __('Buscar') }}
                    </button>
                </form>

                @if (mb_strlen($searchQuery) >= 2)
                    @if ($searchResults->isEmpty())
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('No hay creadores que coincidan.') }}</p>
                    @else
                        <ul class="mt-4 divide-y divide-gray-100 dark:divide-gray-700 border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden">
                            @foreach ($searchResults as $c)
                                <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 bg-gray-50/50 dark:bg-gray-900/40">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="{{ $c->avatar_url }}" alt="" class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-gray-600 shrink-0">
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $c->name }}</p>
                                            <a href="{{ $c->creator_profile_url }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline truncate block">{{ __('Ver perfil público') }}</a>
                                        </div>
                                    </div>
                                    <form method="post" action="{{ route('following.store-platform') }}" class="shrink-0 flex flex-col items-end gap-2">
                                        @csrf
                                        <input type="hidden" name="creator_id" value="{{ $c->id }}">
                                        <input type="hidden" name="q" value="{{ $searchQuery }}">
                                        <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 cursor-pointer max-w-[14rem] text-right">
                                            <input type="checkbox" name="use_custom_avatar" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 shrink-0">
                                            <span>{{ __('Foto personalizada (no sincronizar)') }}</span>
                                        </label>
                                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500">
                                            <x-feather-icon name="user-plus" class="w-4 h-4" />
                                            {{ __('Añadir a mi lista') }}
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">{{ __('Mi lista') }}</h3>
                @if ($entries->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Aún no tienes entradas. Usa el buscador para creadores del sitio o «Añadir cuenta TikTok» para cuentas que no estén registradas aquí.') }}</p>
                @else
                    <ul class="space-y-4">
                        @foreach ($entries as $entry)
                            <li class="flex flex-col lg:flex-row lg:items-start gap-4 p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/30">
                                <div class="flex gap-3 min-w-0 flex-1">
                                    <div class="shrink-0">
                                        @if ($entry->isPlatform() && $entry->platformUser)
                                            <img src="{{ filled($entry->avatar_url) ? $entry->avatar_url : $entry->platformUser->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800" width="48" height="48" loading="lazy">
                                        @elseif ($entry->isExternal() && filled($entry->avatar_url))
                                            <img src="{{ $entry->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800" width="48" height="48" loading="lazy" referrerpolicy="no-referrer">
                                        @else
                                            <span class="inline-flex w-12 h-12 items-center justify-center rounded-full border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-800 text-gray-400" aria-hidden="true">
                                                <x-feather-icon name="user" class="w-6 h-6" />
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        @if ($entry->isPlatform())
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-violet-100 text-violet-800 dark:bg-violet-900/50 dark:text-violet-200">{{ __('Plataforma') }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200">{{ __('Externa') }}</span>
                                            @if ($entry->socialNetwork)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->socialNetwork->name }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $entry->displayTitle() }}</p>
                                    @if ($entry->isPlatform() && filled($entry->remote_display_name))
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Nombre en TikTok') }}: {{ $entry->remote_display_name }}</p>
                                    @endif
                                    @if ($entry->latestFollowerSnapshot && $entry->latestFollowerSnapshot->follower_count !== null)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Seguidores (última lectura)') }}: {{ number_format($entry->latestFollowerSnapshot->follower_count) }} · {{ $entry->latestFollowerSnapshot->recorded_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
                                    @endif
                                    <a href="{{ $entry->url }}" target="_blank" rel="noopener" title="{{ $entry->url }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline break-all">
                                        @if ($entry->isExternal() && filled($entry->username))
                                            {{ '@'.ltrim((string) $entry->username, '@') }}
                                        @else
                                            {{ $entry->url }}
                                        @endif
                                    </a>
                                    @if ($entry->note)
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $entry->note }}</p>
                                    @endif
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    <a href="{{ route('following.edit', $entry) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 text-sm hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <x-feather-icon name="edit-2" class="w-4 h-4" />
                                        {{ __('Editar') }}
                                    </a>
                                    <form method="post" action="{{ route('following.destroy.post', $entry) }}" class="inline" onsubmit="return confirm(@json(__('¿Eliminar esta entrada de tu lista?')));">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-900 text-red-700 dark:text-red-300 text-sm hover:bg-red-50 dark:hover:bg-red-950/40">
                                            <x-feather-icon name="trash-2" class="w-4 h-4" />
                                            {{ __('Quitar') }}
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-creator-layout>
