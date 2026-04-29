<x-administrator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar') }} {{ $locale }}.json
            </h2>
            <a href="{{ route('lang.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                ← {{ __('Back to translations') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('lang.update', $locale) }}" method="POST" id="lang-form">
                        @csrf
                        @method('PUT')

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ count($pairs) }} {{ __('claves') }}. {{ __('Cada fila es un par clave (key) y texto traducido (value). Al guardar se reescribe el JSON.') }}
                        </p>

                        @if(count($pairs) > 0)
                            <div class="sticky top-0 z-10 py-3 -mx-6 px-6 mb-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-600">
                                <label for="filter-keys" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Filtrar claves') }}</label>
                                <input type="text" id="filter-keys" placeholder="{{ __('Escriba 2 o más caracteres para filtrar por clave') }}" autocomplete="off"
                                    class="w-full max-w-md text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-3 py-2">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" id="filter-summary"></p>
                            </div>

                            <div class="overflow-x-auto max-h-[60vh] overflow-y-auto">
                                <table class="w-full min-w-[600px] border-collapse">
                                    <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800 z-[1]">
                                        <tr>
                                            <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 py-2 pr-4 w-[35%]">{{ __('Clave') }}</th>
                                            <th class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 py-2 pl-4 w-[65%]">{{ __('Texto') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pairs-container">
                                        @foreach($pairs as $index => $pair)
                                            <tr class="lang-row border-b border-gray-200 dark:border-gray-600" data-key="{{ e($pair['key']) }}">
                                                <td class="align-top py-2 pr-4">
                                                    <input type="text" name="keys[]" value="{{ old('keys.'.$index, $pair['key']) }}" readonly
                                                        class="w-full text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 cursor-not-allowed px-2 py-1.5">
                                                </td>
                                                <td class="align-top py-2 pl-4">
                                                    <textarea name="values[]" rows="2" class="w-full text-sm rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 px-2 py-1.5">{{ old('values.'.$index, $pair['value']) }}</textarea>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('El archivo está vacío o no tiene pares clave-valor.') }}</p>
                        @endif

                        @if(count($pairs) > 0)
                            <div class="flex gap-2 mt-6 sticky bottom-0 bg-white dark:bg-gray-800 pt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                    {{ __('Guardar') }}
                                </button>
                                <a href="{{ route('lang.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        @else
                            <div class="mt-4">
                                <a href="{{ route('lang.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Volver a traducciones') }}</a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(count($pairs) > 0)
    <script>
    (function() {
        var filterInput = document.getElementById('filter-keys');
        var summaryEl = document.getElementById('filter-summary');
        var rows = document.querySelectorAll('#pairs-container .lang-row');
        var total = rows.length;

        function updateFilter() {
            var q = (filterInput.value || '').trim();
            var minChars = 2;
            var visible = 0;
            if (q.length >= minChars) {
                var lower = q.toLowerCase();
                rows.forEach(function(row) {
                    var key = (row.getAttribute('data-key') || '').toLowerCase();
                    var show = key.indexOf(lower) !== -1;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                summaryEl.textContent = visible + ' de ' + total + ' {{ __("claves") }}';
            } else {
                rows.forEach(function(row) { row.style.display = ''; });
                visible = total;
                summaryEl.textContent = q.length === 1 ? '{{ __("Escriba 2 o más caracteres para filtrar por clave") }}' : '';
            }
        }
        if (filterInput) {
            filterInput.addEventListener('input', updateFilter);
            filterInput.addEventListener('keyup', updateFilter);
        }
    })();
    </script>
    @endif
</x-administrator-layout>
