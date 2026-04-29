<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar horario') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('schedules.update', $schedule) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @php
                        $daysOptions = ['mon' => __('Lunes'), 'tue' => __('Martes'), 'wed' => __('Miércoles'), 'thu' => __('Jueves'), 'fri' => __('Viernes'), 'sat' => __('Sábado'), 'sun' => __('Domingo')];
                        $oldDays = old('days', $schedule->days ?? []);
                    @endphp
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Días') }}</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($daysOptions as $value => $label)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="days[]" value="{{ $value }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"
                                        {{ in_array($value, $oldDays) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('days')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-6">
                        <label for="time" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Hora') }}</label>
                        <input type="time" id="time" name="time" value="{{ old('time', \Carbon\Carbon::parse($schedule->time)->format('H:i')) }}" required
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full max-w-xs">
                        @error('time')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-500">
                            {{ __('Actualizar') }}
                        </button>
                        <a href="{{ route('schedules.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-creator-layout>
