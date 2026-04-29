<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('personal-links.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <x-feather-icon name="chevron-left" class="w-5 h-5" />
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nuevo enlace') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('personal-links.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Texto del enlace') }}</label>
                        <input type="text" name="label" id="label" value="{{ old('label') }}" required maxlength="255" placeholder="{{ __('Ej. Mi tienda, Portfolio, YouTube…') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('label')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('URL') }}</label>
                        <input type="url" name="url" id="url" value="{{ old('url') }}" required maxlength="2048" placeholder="https://"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('url')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Orden') }}</label>
                        <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" step="1"
                            class="mt-1 block w-full max-w-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Números más bajos aparecen primero en el perfil público.') }}</p>
                        @error('sort_order')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-500">
                            {{ __('Guardar') }}
                        </button>
                        <a href="{{ route('personal-links.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-creator-layout>
