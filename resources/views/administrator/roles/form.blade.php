<x-administrator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $role ? __('Editar rol') : __('Nuevo rol') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ $role ? route('roles.update', $role) : route('roles.store') }}" method="POST">
                        @csrf
                        @if($role) @method('PUT') @endif
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Nombre del rol') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $role?->name) }}" required
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full"
                                    placeholder="ej. admin, editor">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="guard_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Guard') }} <span class="text-red-500">*</span></label>
                                <select name="guard_name" id="guard_name" required
                                    class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 w-full max-w-xs">
                                    @foreach (['web' => 'web', 'sanctum' => 'sanctum'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('guard_name', $guardName) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Sesión web (Fortify/Jetstream) frente a API token (Sanctum). Los permisos deben existir para el mismo guard si los asignas.') }}</p>
                                @error('guard_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Permisos') }}</span>
                                @php
                                    $grouped = $permissions->groupBy(fn ($p) => explode('.', $p->name)[0] ?? 'general');
                                    $rolePermissionIds = $rolePermissionIds ?? [];
                                @endphp
                                <div class="space-y-3 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    @foreach($grouped as $group => $perms)
                                        <div>
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">{{ ucfirst($group) }}</p>
                                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($perms as $p)
                                                    <label class="flex items-center gap-2 cursor-pointer">
                                                        <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"
                                                            {{ in_array($p->id, $rolePermissionIds) ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ renombrarPermiso($p->name) }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                    {{ $role ? __('Guardar') : __('Crear') }}
                                </button>
                                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                    {{ __('Cancelar') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($role)
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm">
                                    {{ __('Eliminar rol') }} {{ $role->name }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-administrator-layout>
