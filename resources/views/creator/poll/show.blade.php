<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ver encuesta') }}
            </h2>
            <a href="{{ route('polls.edit', $poll) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                <x-feather-icon name="edit-2" class="w-4 h-4" />{{ __('Editar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Pregunta') }}</p>
                <p class="font-medium text-gray-900 dark:text-white mt-1 text-lg">{{ $poll->question }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ __('Tipo') }}</p>
                <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $poll->type === 'yes_no' ? __('Sí / No') : __('Múltiple opción') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ __('Estado') }}</p>
                <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $poll->is_active ? __('Activa') : __('Inactiva') }}</p>
                @if ($poll->expires_at)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ __('Expira') }}</p>
                    <p class="font-medium text-gray-900 dark:text-white mt-1">{{ $poll->expires_at->format('d/m/Y H:i') }}</p>
                @endif
                @if ($poll->pollOptions->isNotEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ __('Opciones') }}</p>
                    <ul class="mt-2 space-y-1">
                        @foreach ($poll->pollOptions as $option)
                            <li class="text-gray-900 dark:text-white">{{ $option->text }}</li>
                        @endforeach
                    </ul>
                @endif
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('polls.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('Volver a encuestas') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
