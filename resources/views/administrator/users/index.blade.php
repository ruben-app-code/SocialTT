<x-administrator-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Usuarios') }}
            </h2>
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition w-fit">
                {{ __('Crear usuario') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
            @endif

            <div class="mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700">
                <form method="get" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-3">
                    <div class="flex-1 min-w-0">
                        <label for="users-filter-q" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Filtrar por texto') }}</label>
                        <input type="search" name="q" id="users-filter-q" value="{{ $q }}" maxlength="120" placeholder="{{ __('Nombre o correo…') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-gray-500">
                            {{ __('Buscar') }}
                        </button>
                        @if($q !== '')
                            <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-600">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-16">{{ __('Foto') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Name') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Email') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Roles') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Activo') }}</th>
                                <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('Acción') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users as $u)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $u->id }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap align-middle">
                                        @if (filled($u->profile_photo_path))
                                            <img src="{{ $u->avatar_url }}" alt="" class="h-10 w-10 rounded-full object-cover border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 shrink-0" width="40" height="40" loading="lazy" />
                                        @else
                                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">{{ $u->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $u->email }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">{{ $u->roles->pluck('name')->join(', ') ?: '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center align-middle user-active-field">
                                        <span class="inline-flex items-center justify-center js-user-active-wrap" title="@if($u->id === auth()->id()){{ __('No puede desactivar su propia cuenta desde aquí.') }}@elseif($u->isSuperAdmin()){{ __('No puede desactivar a un usuario con rol SuperAdmin.') }}@else{{ $u->active ? __('Activo') : __('Inactivo') }}@endif">
                                            <label class="switch">
                                                <input
                                                    type="checkbox"
                                                    class="js-user-active-toggle"
                                                    data-url="{{ route('users.update-active', $u) }}"
                                                    {{ $u->active ? 'checked' : '' }}
                                                    @if($u->id === auth()->id() || $u->isSuperAdmin()) disabled @endif
                                                >
                                                <span class="slider round"></span>
                                            </label>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm">
                                        <div class="inline-flex flex-wrap items-center justify-end gap-2">
                                            <a href="{{ route('users.edit', $u) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-wide hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800">
                                                {{ __('Acción') }}
                                            </a>
                                            @if($u->id !== auth()->id())
                                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar este usuario?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-xs font-medium">{{ __('Delete') }}</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500 text-xs" title="{{ __('No puede eliminar su propia cuenta.') }}">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $q !== '' ? __('No hay usuarios que coincidan con el filtro.') : __('No hay usuarios.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.js-user-active-toggle').forEach(function (input) {
        input.addEventListener('change', function () {
            var url = this.dataset.url;
            var label = this.closest('.js-user-active-wrap');
            var checked = this.checked;
            var prev = !checked;
            label.classList.add('is-loading');
            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ active: checked })
            })
            .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, status: r.status, data: data }; }); })
            .then(function (res) {
                label.classList.remove('is-loading');
                if (!res.ok) {
                    input.checked = prev;
                    alert(res.data.message || '{{ __('Error al actualizar.') }}');
                    return;
                }
                input.checked = !!res.data.active;
            })
            .catch(function () {
                label.classList.remove('is-loading');
                input.checked = prev;
                alert('{{ __('Error de red.') }}');
            });
        });
    });
    </script>
</x-administrator-layout>
