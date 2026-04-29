<x-administrator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $permission ? __('Editar permiso') : __('Nuevo permiso') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ $permission ? route('permisos.update', $permission) : route('permisos.store') }}" method="POST">
                        @csrf
                        @if($permission) @method('PUT') @endif
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Nombre') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $permission?->name) }}" required
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full"
                                    placeholder="ej. aplicaciones.crear">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="guard_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Guard') }}</label>
                                <input type="text" name="guard_name" id="guard_name" value="{{ old('guard_name', $guardName) }}" readonly
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded-md shadow-sm w-full cursor-not-allowed"
                                    placeholder="web">
                                @error('guard_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                    {{ $permission ? __('Guardar') : __('Crear') }}
                                </button>
                                <a href="{{ route('permisos.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($permission)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <form action="{{ route('permisos.destroy', $permission) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar {{ $permission->name }}? Se quitará de todos los roles.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                    {{ __('Eliminar') }} {{ renombrarPermiso($permission->name) }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-administrator-layout>
