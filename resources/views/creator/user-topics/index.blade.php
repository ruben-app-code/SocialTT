<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mis temas') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">
                    {{ __('Elige los temas con los que te identificas como creador. Así tus seguidores podrán encontrarte por categoría.') }}
                </p>

                <form action="{{ route('user-topics.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">
                        @foreach ($rootTopics as $root)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">{{ $root->name }}</p>
                                <div class="flex flex-wrap gap-3">
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-colors
                                        {{ in_array($root->id, $myTopicIds) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-500' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        <input type="checkbox" name="topic_ids[]" value="{{ $root->id }}"
                                            {{ in_array($root->id, $myTopicIds) ? 'checked' : '' }}
                                            class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('General') }}</span>
                                    </label>
                                    @foreach ($root->children as $topic)
                                        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition-colors
                                            {{ in_array($topic->id, $myTopicIds) ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-500' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                            <input type="checkbox" name="topic_ids[]" value="{{ $topic->id }}"
                                                {{ in_array($topic->id, $myTopicIds) ? 'checked' : '' }}
                                                class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $topic->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Si no marcas ninguno, se quitarán todos.') }}
                    </p>
                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-500">
                            {{ __('Guardar temas') }}
                        </button>
                        <a href="{{ route('profile.show') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-creator-layout>
