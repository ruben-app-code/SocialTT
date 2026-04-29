<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Configuración') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
            @endif

            {{-- Visor: base de datos --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Configuración guardada (base de datos)') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Valores almacenados para tu usuario.') }}</p>
                </div>
                <div class="overflow-x-auto">
                    @if($settingsFromDb->isEmpty())
                        <p class="p-6 text-sm text-gray-500 dark:text-gray-400">{{ __('Sin registros. Al elegir un tema abajo se creará la primera configuración.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Clave') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Valor') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($settingsFromDb as $row)
                                    <tr>
                                        <td class="px-6 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $row->key }}</td>
                                        <td class="px-6 py-3 text-sm text-gray-700 dark:text-gray-300 break-all">{{ $row->value ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Visor: sesión --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Variables en sesión') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Cargadas al iniciar sesión o al guardar. Útiles para la app en tiempo real.') }}</p>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">session('theme')</span>
                        <code class="block mt-1 px-3 py-2 rounded bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ $sessionTheme ?? '—' }}</code>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">session('user_settings_loaded')</span>
                        <code class="block mt-1 px-3 py-2 rounded bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">{{ $sessionLoaded ? 'true' : 'false' }}</code>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">session('user_settings')</span>
                        <pre class="mt-1 p-3 rounded bg-gray-100 dark:bg-gray-900 text-xs text-gray-800 dark:text-gray-200 overflow-x-auto">{{ json_encode($sessionSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            </div>

            {{-- Editar tema --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Tema de la interfaz') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('Se guarda en tu cuenta y en sesión al guardar.') }}</p>
                </div>
                <div class="p-6">
                    <form action="{{ route('configuration.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <fieldset>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="theme" value="light" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700" {{ old('theme', $theme) === 'light' ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ __('Claro') }}</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="theme" value="dark" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700" {{ old('theme', $theme) === 'dark' ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ __('Oscuro') }}</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="theme" value="system" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700" {{ old('theme', $theme) === 'system' ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ __('Sistema') }}</span>
                                </label>
                            </div>
                            @error('theme')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </fieldset>
                        <div class="mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                {{ __('Guardar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
