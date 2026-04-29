<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nueva encuesta') }}
        </h2>
    </x-slot>

    @php
        $oldType = old('type', 'yes_no');
        $oldOptions = old('options');
        if (! is_array($oldOptions)) {
            $oldOptions = ['', ''];
        }
        while (count($oldOptions) < 2) {
            $oldOptions[] = '';
        }
    @endphp

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('polls.store') }}" method="POST" id="poll-form">
                    @csrf
                    <div class="mb-4">
                        <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Pregunta') }}</label>
                        <textarea id="question" name="question" rows="2" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full" placeholder="{{ __('¿Qué tipo de contenido te gustaría ver más?') }}">{{ old('question') }}</textarea>
                        @error('question')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Tipo') }}</label>
                        <select name="type" id="poll-type" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                            <option value="yes_no" @selected($oldType === 'yes_no')>{{ __('Sí / No') }} ({{ __('dos opciones') }})</option>
                            <option value="multiple" @selected($oldType === 'multiple')>{{ __('Múltiple opción') }}</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Sí/No: dos textos (ej. Sí y No). Múltiple: añade todas las opciones de respuesta.') }}</p>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="poll-fields-yes-no" class="mb-4 space-y-3 @if ($oldType !== 'yes_no') hidden @endif">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Textos de las dos opciones') }}</p>
                        <div>
                            <label for="yes_text" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Primera opción') }}</label>
                            <input type="text" name="yes_text" id="yes_text" value="{{ old('yes_text', __('Sí')) }}" maxlength="255" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                            @error('yes_text')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_text" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Segunda opción') }}</label>
                            <input type="text" name="no_text" id="no_text" value="{{ old('no_text', __('No')) }}" maxlength="255" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                            @error('no_text')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="poll-fields-multiple" class="mb-4 @if ($oldType !== 'multiple') hidden @endif">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Opciones de respuesta') }}</label>
                            <button type="button" id="poll-add-option" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('+ Añadir opción') }}</button>
                        </div>
                        <div id="poll-multiple-options" class="space-y-2">
                            @foreach ($oldOptions as $i => $optText)
                                <div class="poll-option-row flex gap-2 items-center">
                                    <input type="text" name="options[]" value="{{ $optText }}" maxlength="255" placeholder="{{ __('Opción :num', ['num' => $i + 1]) }}" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @if ($i >= 2)
                                        <button type="button" class="poll-remove-option shrink-0 p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="{{ __('Quitar') }}">&times;</button>
                                    @else
                                        <span class="w-9 shrink-0"></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @error('options')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('options.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Encuesta activa') }}</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Fecha de expiración') }} ({{ __('opcional') }})</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full max-w-xs">
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-500">
                            {{ __('Guardar') }}
                        </button>
                        <a href="{{ route('polls.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var typeSel = document.getElementById('poll-type');
            var yesNo = document.getElementById('poll-fields-yes-no');
            var multiple = document.getElementById('poll-fields-multiple');
            var container = document.getElementById('poll-multiple-options');
            var addBtn = document.getElementById('poll-add-option');

            function sync() {
                if (!typeSel || !yesNo || !multiple) return;
                var v = typeSel.value;
                yesNo.classList.toggle('hidden', v !== 'yes_no');
                multiple.classList.toggle('hidden', v !== 'multiple');
            }

            typeSel && typeSel.addEventListener('change', sync);
            sync();

            function addRemoveHandler(row) {
                var btn = row.querySelector('.poll-remove-option');
                if (btn) btn.addEventListener('click', function () { row.remove(); });
            }

            document.querySelectorAll('#poll-multiple-options .poll-option-row').forEach(addRemoveHandler);

            addBtn && addBtn.addEventListener('click', function () {
                var row = document.createElement('div');
                row.className = 'poll-option-row flex gap-2 items-center';
                row.innerHTML = '<input type="text" name="options[]" value="" maxlength="255" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="{{ __('Nueva opción') }}">' +
                    '<button type="button" class="poll-remove-option shrink-0 p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="{{ __('Quitar') }}">&times;</button>';
                container.appendChild(row);
                addRemoveHandler(row);
            });
        })();
    </script>
</x-creator-layout>
