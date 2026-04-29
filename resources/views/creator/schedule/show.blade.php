<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Ver horario') }}
            </h2>
            <a href="{{ route('schedules.edit', $schedule) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                <x-feather-icon name="edit-2" class="w-4 h-4" />{{ __('Editar') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @php
                $daysMap = ['mon' => __('Lunes'), 'tue' => __('Martes'), 'wed' => __('Miércoles'), 'thu' => __('Jueves'), 'fri' => __('Viernes'), 'sat' => __('Sábado'), 'sun' => __('Domingo')];
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Días') }}</p>
                <p class="font-medium text-gray-900 dark:text-white mt-1">
                    {{ collect($schedule->days)->map(fn ($d) => $daysMap[$d] ?? $d)->join(', ') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">{{ __('Hora') }}</p>
                <p class="font-medium text-gray-900 dark:text-white mt-1">{{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}</p>
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('schedules.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">{{ __('Volver a horarios') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
