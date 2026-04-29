<x-follower-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de seguidor') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-gray-600 dark:text-gray-300">{{ __('Vista base para el rol seguidor. Amplía aquí el contenido.') }}</p>
            </div>
        </div>
    </div>
</x-follower-layout>
