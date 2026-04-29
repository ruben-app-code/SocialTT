<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Redes sociales') }}
            </h2>
            <a href="{{ route('social-accounts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Añadir cuenta') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('socialAccount.id'))
                <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
                    {{ __('Cuenta guardada correctamente.') }}
                </div>
            @endif
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 p-4 text-sm text-indigo-800 dark:text-indigo-200">
                    {{ session('status') }}
                </div>
            @endif

            <p class="mb-4 text-sm text-gray-600 dark:text-gray-400 max-w-3xl">
                {{ __('«Verificada» es un indicador manual: confirmas tú que el usuario enlazado es tu cuenta real en esa red. Más adelante se podría automatizar (OAuth de la red, revisión admin, etc.).') }}
            </p>

            @if ($socialAccounts->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Aún no tienes redes sociales vinculadas.') }}</p>
                    <a href="{{ route('social-accounts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Añadir primera cuenta') }}
                    </a>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-xl border border-gray-200 dark:border-gray-700">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($socialAccounts as $account)
                            <li class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-gray-600 dark:text-gray-300 font-semibold">
                                        {{ Str::limit($account->socialNetwork->name ?? '-', 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $account->display_name ?: ($account->socialNetwork->name ?? __('Red')) }}
                                        </p>
                                        @if (filled($account->display_name))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $account->socialNetwork->name ?? '' }}</p>
                                        @endif
                                        <p class="text-sm text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-2">
                                            <span>{{ '@'.$account->username }}</span>
                                            @if ($account->is_primary)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wide bg-amber-100 text-amber-900 dark:bg-amber-900/40 dark:text-amber-200">{{ __('Principal') }}</span>
                                            @endif
                                        </p>
                                        @if ($account->topics->isNotEmpty())
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @foreach ($account->topics->take(4) as $t)
                                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200">{{ $t->name }}</span>
                                                @endforeach
                                                @if ($account->topics->count() > 4)
                                                    <span class="text-[10px] text-gray-500">+{{ $account->topics->count() - 4 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($account->current_status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($account->current_status === 'deleted') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                @elseif($account->current_status === 'blocked') bg-orange-100 text-orange-900 dark:bg-orange-900/30 dark:text-orange-200
                                                @else bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                                @endif">
                                                @if ($account->current_status === 'blocked')
                                                    {{ __('Bloqueada') }}
                                                @else
                                                    {{ $account->current_status }}
                                                @endif
                                            </span>
                                            @if ($account->current_status === 'blocked' && $account->block)
                                                <span class="text-[10px] text-gray-500 dark:text-gray-400 block sm:inline sm:ml-1">
                                                    {{ __('Hasta :fecha', ['fecha' => $account->block->activates_at->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}
                                                </span>
                                            @endif
                                            @if ($account->is_verified)
                                                <span class="text-xs text-indigo-600 dark:text-indigo-400">{{ __('Verificado') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    @if ($account->is_verified)
                                        <form action="{{ route('social-accounts.verification', $account) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_verified" value="0">
                                            <button type="submit" class="text-xs px-2 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">{{ __('Quitar verificación') }}</button>
                                        </form>
                                    @else
                                        <form action="{{ route('social-accounts.verification', $account) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_verified" value="1">
                                            <button type="submit" class="text-xs px-2 py-1 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">{{ __('Marcar verificada') }}</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('social-accounts.show', $account) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">{{ __('Ver') }}</a>
                                    <a href="{{ route('social-accounts.edit', $account) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">{{ __('Editar') }}</a>
                                    <form action="{{ route('social-accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar esta cuenta?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-sm text-red-600 dark:text-red-400 hover:text-red-500"><x-feather-icon name="trash-2" class="w-4 h-4" />{{ __('Eliminar') }}</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">&larr; {{ __('Volver al panel') }}</a>
            </div>
        </div>
    </div>
</x-creator-layout>
