<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Encuestas') }}
            </h2>
            <a href="{{ route('polls.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Nueva encuesta') }}
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

            @if ($polls->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('Aún no tienes encuestas.') }}</p>
                    <a href="{{ route('polls.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        <x-feather-icon name="plus" class="w-4 h-4" />{{ __('Crear primera encuesta') }}
                    </a>
                </div>
            @else
                <ul class="space-y-4">
                    @foreach ($polls as $poll)
                        <li class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-4 flex items-center justify-between flex-wrap gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $poll->question }}</p>
                                <div class="flex items-center gap-2 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $poll->type === 'yes_no' ? __('Sí/No') : __('Múltiple') }}</span>
                                    <span>·</span>
                                    <span>{{ $poll->poll_options_count }} {{ __('opciones') }}</span>
                                    @if ($poll->expires_at)
                                        <span>·</span>
                                        <span>{{ __('Expira') }} {{ $poll->expires_at->format('d/m/Y') }}</span>
                                    @endif
                                    @if ($poll->is_active)
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">{{ __('Activa') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('polls.show', $poll) }}" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                    <x-feather-icon name="eye" class="w-4 h-4" />
                                </a>
                                <a href="{{ route('polls.edit', $poll) }}" class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                    <x-feather-icon name="edit-2" class="w-4 h-4" />
                                </a>
                                <form action="{{ route('polls.destroy', $poll) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar esta encuesta?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                                        <x-feather-icon name="trash-2" class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-6">
                    {{ $polls->links() }}
                </div>
            @endif
        </div>
    </div>
</x-creator-layout>
