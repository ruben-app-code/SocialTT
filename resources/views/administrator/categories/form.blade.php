@php
$iconOptions = ['folder', 'document-text', 'tag', 'star', 'briefcase', 'cube', 'chart-bar', 'clipboard-document-list', 'computer-desktop', 'bookmark', 'flag', 'paint-brush', 'wrench', 'cog', 'rectangle-stack'];
$iconLabels = [
    'folder' => 'Carpeta',
    'document-text' => 'Documento',
    'tag' => 'Etiqueta',
    'star' => 'Estrella',
    'briefcase' => 'Maletín',
    'cube' => 'Cubo',
    'chart-bar' => 'Gráfica',
    'clipboard-document-list' => 'Portapapeles',
    'computer-desktop' => 'Pantalla',
    'bookmark' => 'Marcador',
    'flag' => 'Bandera',
    'paint-brush' => 'Pincel',
    'wrench' => 'Llave',
    'cog' => 'Engranaje',
    'rectangle-stack' => 'Pila',
];
@endphp
<x-administrator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $categoria->exists ? __('Editar categoría') : __('Nueva categoría') }}@if($categoria->exists): {{ $categoria->nombre }}@endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ $categoria->exists ? route('categories.update', $categoria) : route('categories.store') }}" method="POST">
                        @csrf
                        @if($categoria->exists) @method('PUT') @endif
                        <div class="space-y-6">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $categoria->nombre) }}" required maxlength="255"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('nombre')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Icono') }}</span>
                                <div class="flex flex-wrap gap-2">
                                    <label class="flex flex-col items-center gap-1 cursor-pointer">
                                        <input type="radio" name="icon" value="" class="sr-only peer" {{ old('icon', $categoria->icon) === '' || old('icon') === null ? 'checked' : '' }}>
                                        <span class="flex items-center justify-center size-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 text-gray-400">
                                            —
                                        </span>
                                        <span class="text-xs text-gray-500">{{ __('Ninguno') }}</span>
                                    </label>
                                    @foreach($iconOptions as $iconKey)
                                        <label class="flex flex-col items-center gap-1 cursor-pointer">
                                            <input type="radio" name="icon" value="{{ $iconKey }}" class="sr-only peer" {{ old('icon', $categoria->icon) === $iconKey ? 'checked' : '' }}>
                                            <span class="flex items-center justify-center size-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 text-gray-600 dark:text-gray-300">
                                                <x-category-icon :name="$iconKey" class="size-6" />
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $iconLabels[$iconKey] ?? $iconKey }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="bg" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Color de fondo') }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="bg" name="bg" value="{{ old('bg', $categoria->bg ?? '#1E85FF') }}"
                                            class="h-10 w-14 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-1 bg-white dark:bg-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400" id="bg-hex">{{ old('bg', $categoria->bg ?? '#1E85FF') }}</span>
                                    </div>
                                    @error('bg')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Color de texto') }}</label>
                                    <div class="flex items-center gap-2">
                                        <input type="color" id="color" name="color" value="{{ old('color', $categoria->color ?? '#ffffff') }}"
                                            class="h-10 w-14 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-1 bg-white dark:bg-gray-700">
                                        <span class="text-sm text-gray-500 dark:text-gray-400" id="color-hex">{{ old('color', $categoria->color ?? '#ffffff') }}</span>
                                    </div>
                                    @error('color')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="pt-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('Vista previa') }}:</p>
                                <span id="preview-badge" class="inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium" style="background-color: {{ old('bg', $categoria->bg ?? '#1E85FF') }}; color: {{ old('color', $categoria->color ?? '#ffffff') }};">
                                    <span id="preview-icon"></span>
                                    <span id="preview-name">{{ old('nombre', $categoria->nombre) ?: __('Nombre') }}</span>
                                </span>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                    {{ $categoria->exists ? __('Save') : __('Create') }}
                                </button>
                                <a href="{{ route('categories.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var bgInput = document.getElementById('bg');
        var bgHex = document.getElementById('bg-hex');
        var colorInput = document.getElementById('color');
        var colorHex = document.getElementById('color-hex');
        var nombreInput = document.getElementById('nombre');
        var previewBadge = document.getElementById('preview-badge');
        var previewName = document.getElementById('preview-name');

        function updatePreview() {
            if (previewBadge && bgInput) previewBadge.style.backgroundColor = bgInput.value;
            if (previewBadge && colorInput) previewBadge.style.color = colorInput.value;
            if (previewName && nombreInput) previewName.textContent = nombreInput.value || '{{ __("Name") }}';
            if (bgHex && bgInput) bgHex.textContent = bgInput.value;
            if (colorHex && colorInput) colorHex.textContent = colorInput.value;
        }
        if (bgInput) bgInput.addEventListener('input', updatePreview);
        if (colorInput) colorInput.addEventListener('input', updatePreview);
        if (nombreInput) nombreInput.addEventListener('input', updatePreview);
        document.querySelectorAll('input[name="icon"]').forEach(function(r) { r.addEventListener('change', updatePreview); });
        updatePreview();
    })();
    </script>
</x-administrator-layout>
