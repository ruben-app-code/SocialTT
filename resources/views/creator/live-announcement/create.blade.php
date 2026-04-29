<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Nuevo anuncio de directo') }}</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <form action="{{ route('live-announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Título') }}</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white w-full" placeholder="{{ __('Ej: Q&A en vivo') }}">
                        @error('title')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Fecha y hora') }}</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}" required class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white w-full max-w-xs">
                        @error('scheduled_at')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Descripción') }} ({{ __('opcional') }})</label>
                        <textarea id="description" name="description" rows="3" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white w-full">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-500">{{ __('Guardar') }}</button>
                        <a href="{{ route('live-announcements.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-creator-layout>
