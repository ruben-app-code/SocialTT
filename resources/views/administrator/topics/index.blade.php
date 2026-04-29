<x-administrator-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Temas') }}
            </h2>
            <a href="{{ route('admin.topics.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                {{ __('Nuevo tema') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                @if ($roots->isEmpty())
                    <p class="p-8 text-center text-gray-500 dark:text-gray-400">{{ __('No hay temas todavía. Crea el primero.') }}</p>
                @else
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($roots as $root)
                            <li class="p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $root->name }}</span>
                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $root->slug }}</span>
                                        <span class="ml-2 inline-flex px-2 py-0.5 rounded text-[10px] font-semibold uppercase bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200">{{ __('Principal') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.topics.edit', $root) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Editar') }}</a>
                                        <form action="{{ route('admin.topics.destroy', $root) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar este tema?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">{{ __('Eliminar') }}</button>
                                        </form>
                                    </div>
                                </div>
                                @if ($root->children->isNotEmpty())
                                    <ul class="mt-3 ml-4 pl-4 border-l-2 border-gray-200 dark:border-gray-600 space-y-2">
                                        @foreach ($root->children as $child)
                                            <li class="flex flex-wrap items-center justify-between gap-2 text-sm">
                                                <div>
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $child->name }}</span>
                                                    <span class="ml-2 text-xs text-gray-500 font-mono">{{ $child->slug }}</span>
                                                    <span class="ml-2 text-xs text-gray-400">({{ __('Subtema de') }} {{ $root->name }})</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.topics.edit', $child) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Editar') }}</a>
                                                    <form action="{{ route('admin.topics.destroy', $child) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar este subtema?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">{{ __('Eliminar') }}</button>
                                                    </form>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-administrator-layout>
