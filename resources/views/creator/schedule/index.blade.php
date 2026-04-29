<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Horarios') }}
            </h2>
            <a href="{{ route('schedules.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Nuevo horario') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $daysMap = ['mon' => __('Lun'), 'tue' => __('Mar'), 'wed' => __('Mié'), 'thu' => __('Jue'), 'fri' => __('Vie'), 'sat' => __('Sáb'), 'sun' => __('Dom')];
            @endphp

            @if ($schedules->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Aún no tienes horarios configurados.') }}</p>
                    <a href="{{ route('schedules.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Crear primer horario') }}
                    </a>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach ($schedules as $schedule)
                        <li class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ collect($schedule->days)->map(fn ($d) => $daysMap[$d] ?? $d)->join(', ') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Hora') }}: {{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('schedules.edit', $schedule) }}" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                    <x-feather-icon name="edit-2" class="w-4 h-4" />
                                </a>
                                <form action="{{ route('schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar este horario?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                        <x-feather-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-creator-layout>
