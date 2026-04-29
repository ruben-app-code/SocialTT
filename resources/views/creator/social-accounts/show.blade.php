<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('social-accounts.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <x-feather-icon name="chevron-left" class="w-5 h-5" />
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $socialAccount->socialNetwork->name ?? __('Red social') }}
                </h2>
            </div>
            <a href="{{ route('social-accounts.edit', $socialAccount) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                <x-feather-icon name="edit-2" class="w-4 h-4" />{{ __('Editar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <dl class="divide-y divide-gray-200 dark:divide-gray-700 p-6 space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Red') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $socialAccount->socialNetwork->name ?? '-' }}</dd>
                    </div>
                    @if (filled($socialAccount->display_name))
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Nombre') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $socialAccount->display_name }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Usuario') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ "@".$socialAccount->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('URL') }}</dt>
                        <dd class="mt-1">
                            @if ($socialAccount->url)
                                <a href="{{ $socialAccount->url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline"><x-feather-icon name="external-link" class="w-4 h-4" />{{ $socialAccount->url }}</a>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">—</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Estado') }}</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($socialAccount->current_status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($socialAccount->current_status === 'deleted') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @elseif($socialAccount->current_status === 'blocked') bg-orange-100 text-orange-900 dark:bg-orange-900/30 dark:text-orange-200
                                @else bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                @endif">
                                @if ($socialAccount->current_status === 'blocked')
                                    {{ __('Bloqueada') }}
                                @else
                                    {{ $socialAccount->current_status }}
                                @endif
                            </span>
                        </dd>
                    </div>
                    @if ($socialAccount->block)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Bloqueo') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white space-y-1">
                                <p>{{ __('Bloqueada el :fecha', ['fecha' => $socialAccount->block->blocked_at->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}</p>
                                <p>{{ __('Próxima reactivación: :fecha', ['fecha' => $socialAccount->block->activates_at->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}</p>
                                @if ($socialAccount->isInActiveBlockPeriod())
                                    <p class="text-amber-700 dark:text-amber-300 text-xs">{{ __('En periodo de bloqueo: el perfil público oculta el enlace hasta la fecha indicada (luego vuelve a mostrarse si no cambias el estado).') }}</p>
                                @elseif ($socialAccount->hasBlockPeriodEnded())
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('El plazo de bloqueo ya pasó; el enlace puede mostrarse en público. Puedes marcar la cuenta como activa para quitar el estado bloqueada.') }}</p>
                                @endif
                            </dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Verificada') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $socialAccount->is_verified ? __('Sí') : __('No') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Cuenta principal (esta red)') }}</dt>
                        <dd class="mt-1 text-gray-900 dark:text-white">{{ $socialAccount->is_primary ? __('Sí') : __('No') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Temas de la cuenta') }}</dt>
                        <dd class="mt-1">
                            @if ($socialAccount->topics->isEmpty())
                                <span class="text-gray-500 dark:text-gray-400">—</span>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($socialAccount->topics as $topic)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            {{ $topic->display_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </dd>
                    </div>
                    @if ($socialAccount->last_checked_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Última verificación') }}</dt>
                            <dd class="mt-1 text-gray-900 dark:text-white">{{ $socialAccount->last_checked_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    @endif
                </dl>
                <div class="px-6 pb-6 flex gap-4">
                    <a href="{{ route('social-accounts.edit', $socialAccount) }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500"><x-feather-icon name="edit-2" class="w-4 h-4" />{{ __('Editar') }}</a>
                    <a href="{{ route('social-accounts.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">&larr; {{ __('Volver') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
