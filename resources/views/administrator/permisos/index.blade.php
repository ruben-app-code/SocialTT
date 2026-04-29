<x-administrator-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Administración de permisos') }}
            </h2>
            <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                {{ __('Roles') }}
            </x-nav-link>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
            @endif

            {{-- Permisos por rol --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Permisos por rol') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Rol') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Permisos') }}</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Acción') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($roles as $role)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">{{ $role->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                        @php
                                            $labels = $role->permissions->map(fn ($p) => renombrarPermiso($p->name))->join(', ');
                                        @endphp
                                        {{ $labels ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm">
                                        <a href="{{ route('permisos.role.edit', $role) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">{{ __('Editar permisos') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Listado de permisos del sistema --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Permisos del sistema') }}</h3>
                    <a href="{{ route('permisos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                        {{ __('Nuevo permiso') }}
                    </a>
                </div>
                <div class="p-6">
                    @forelse($permissions as $p)
                        <span class="inline-flex items-center gap-2 mr-2 mb-2">
                            <a href="{{ route('permisos.edit', $p) }}" class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-200 hover:bg-indigo-200 dark:hover:bg-indigo-800/60">
                                {{ renombrarPermiso($p->name) }}
                            </a>
                            <form action="{{ route('permisos.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este permiso? Se quitará de todos los roles.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-bold text-sm" title="{{ __('Eliminar') }}">×</button>
                            </form>
                        </span>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No hay permisos.') }}
                            <a href="{{ route('permisos.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Crear uno') }}</a>
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-administrator-layout>
