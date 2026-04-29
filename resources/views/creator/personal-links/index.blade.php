<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Enlaces personales') }}
            </h2>
            <a href="{{ route('personal-links.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Nuevo enlace') }}
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

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 max-w-2xl">
                {{ __('Añade enlaces a tu web, tienda, Linktree u otras páginas. El orden numérico define cómo se muestran en tu perfil público (menor primero).') }}
            </p>

            @if ($links->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Aún no tienes enlaces personales.') }}</p>
                    <a href="{{ route('personal-links.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Crear primer enlace') }}
                    </a>
                </div>
            @else
                <ul class="space-y-3">
                    @foreach ($links as $link)
                        <li class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 flex flex-wrap items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $link->label }}</p>
                                <a href="{{ $link->url }}" target="_blank" rel="noopener" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline break-all">{{ $link->url }}</a>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Orden') }}: {{ $link->sort_order }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('personal-links.edit', $link) }}" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="{{ __('Editar') }}">
                                    <x-feather-icon name="edit-2" class="w-4 h-4" />
                                </a>
                                <form action="{{ route('personal-links.destroy', $link) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar este enlace?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg" title="{{ __('Eliminar') }}">
                                        <x-feather-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">&larr; {{ __('Volver al panel') }}</a>
            </div>
        </div>
    </div>
</x-creator-layout>
