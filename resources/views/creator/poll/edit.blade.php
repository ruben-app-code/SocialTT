<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar encuesta') }}
        </h2>
    </x-slot>

    @php
        $ordered = $poll->pollOptions->sortBy('id')->values();
        $oldType = old('type', $poll->type);
        $yesText = old('yes_text', $ordered->get(0)?->text ?? __('Sí'));
        $noText = old('no_text', $ordered->get(1)?->text ?? __('No'));
        $optionRows = [];
        if (is_array(old('option_text'))) {
            $texts = old('option_text', []);
            $ids = old('option_id', []);
            foreach ($texts as $i => $t) {
                $optionRows[] = ['text' => $t, 'id' => $ids[$i] ?? null];
            }
        } else {
            foreach ($ordered as $o) {
                $optionRows[] = ['text' => $o->text, 'id' => $o->id];
            }
        }
        if ($oldType === 'multiple' && count($optionRows) < 2) {
            while (count($optionRows) < 2) {
                $optionRows[] = ['text' => '', 'id' => null];
            }
        }
    @endphp

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('polls.update', $poll) }}" method="POST" id="poll-form-edit">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="question" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Pregunta') }}</label>
                        <textarea id="question" name="question" rows="2" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">{{ old('question', $poll->question) }}</textarea>
                        @error('question')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Tipo') }}</label>
                        <select name="type" id="poll-type-edit" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                            <option value="yes_no" @selected($oldType === 'yes_no')>{{ __('Sí / No') }} ({{ __('dos opciones') }})</option>
                            <option value="multiple" @selected($oldType === 'multiple')>{{ __('Múltiple opción') }}</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Sí/No: dos textos. Múltiple: varias opciones; puedes añadir o quitar filas.') }}</p>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="poll-fields-yes-no-edit" class="mb-4 space-y-3 @if ($oldType !== 'yes_no') hidden @endif">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Textos de las dos opciones') }}</p>
                        <div>
                            <label for="yes_text" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Primera opción') }}</label>
                            <input type="text" name="yes_text" id="yes_text" value="{{ $yesText }}" maxlength="255" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                            @error('yes_text')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_text" class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Segunda opción') }}</label>
                            <input type="text" name="no_text" id="no_text" value="{{ $noText }}" maxlength="255" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full">
                            @error('no_text')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="poll-fields-multiple-edit" class="mb-4 @if ($oldType !== 'multiple') hidden @endif">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Opciones de respuesta') }}</label>
                            <button type="button" id="poll-add-option-edit" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('+ Añadir opción') }}</button>
                        </div>
                        <div id="poll-multiple-options-edit" class="space-y-2">
                            @foreach ($optionRows as $i => $row)
                                <div class="poll-option-row-edit flex gap-2 items-center">
                                    <input type="hidden" name="option_id[]" value="{{ $row['id'] ?? '' }}">
                                    <input type="text" name="option_text[]" value="{{ $row['text'] }}" maxlength="255" placeholder="{{ __('Opción :num', ['num' => $i + 1]) }}" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                    @if ($i >= 2)
                                        <button type="button" class="poll-remove-option-edit shrink-0 p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="{{ __('Quitar') }}">&times;</button>
                                    @else
                                        <span class="w-9 shrink-0"></span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @error('option_text')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('option_text.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $poll->is_active) ? 'checked' : '' }} class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Encuesta activa') }}</span>
                        </label>
                    </div>
                    <div class="mb-6">
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Fecha de expiración') }} ({{ __('opcional') }})</label>
                        <input type="datetime-local" id="expires_at" name="expires_at" value="{{ old('expires_at', $poll->expires_at?->format('Y-m-d\TH:i')) }}" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full max-w-xs">
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-500">
                            {{ __('Actualizar') }}
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
            var typeSel = document.getElementById('poll-type-edit');
            var yesNo = document.getElementById('poll-fields-yes-no-edit');
            var multiple = document.getElementById('poll-fields-multiple-edit');
            var container = document.getElementById('poll-multiple-options-edit');
            var addBtn = document.getElementById('poll-add-option-edit');

            function sync() {
                if (!typeSel || !yesNo || !multiple) return;
                var v = typeSel.value;
                yesNo.classList.toggle('hidden', v !== 'yes_no');
                multiple.classList.toggle('hidden', v !== 'multiple');
            }

            typeSel && typeSel.addEventListener('change', sync);
            sync();

            function addRemoveHandler(row) {
                var btn = row.querySelector('.poll-remove-option-edit');
                if (btn) btn.addEventListener('click', function () { row.remove(); });
            }

            document.querySelectorAll('#poll-multiple-options-edit .poll-option-row-edit').forEach(addRemoveHandler);

            addBtn && addBtn.addEventListener('click', function () {
                var row = document.createElement('div');
                row.className = 'poll-option-row-edit flex gap-2 items-center';
                row.innerHTML = '<input type="hidden" name="option_id[]" value="">' +
                    '<input type="text" name="option_text[]" value="" maxlength="255" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="{{ __('Nueva opción') }}">' +
                    '<button type="button" class="poll-remove-option-edit shrink-0 p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded" title="{{ __('Quitar') }}">&times;</button>';
                container.appendChild(row);
                addRemoveHandler(row);
            });
        })();
    </script>
</x-creator-layout>
