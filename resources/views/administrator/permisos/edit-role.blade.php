<x-administrator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar permisos del rol') }}: {{ $role->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('permisos.role.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @php
                            $grouped = $permissions->groupBy(fn ($p) => explode('.', $p->name)[0] ?? 'general');
                        @endphp
                        <div class="space-y-4">
                            @foreach($grouped as $group => $perms)
                                <fieldset class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                    <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 px-2">{{ ucfirst($group) }}</legend>
                                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2 mt-2">
                                        @foreach($perms as $p)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="permissions[]" value="{{ $p->id }}" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"
                                                    {{ in_array($p->id, $rolePermissionIds) ? 'checked' : '' }}>
                                                <span class="text-sm text-gray-800 dark:text-gray-200">
                                                    {{ renombrarPermiso($p->name) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>
                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                {{ __('Guardar permisos') }}
                            </button>
                            <a href="{{ route('permisos.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Cancelar') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-administrator-layout>
