<x-creator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Roles del usuario actual --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Roles') }}
                    </h3>
                    @php($userRoles = Auth::user()->roles)
                    @if($userRoles->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No tiene roles asignados.') }}</p>
                    @else
                        <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300">
                            @foreach($userRoles as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Permisos del usuario actual --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        {{ __('Permisos') }}
                    </h3>
                    @php($userPermissions = Auth::user()->getAllPermissions())
                    @if($userPermissions->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No tiene permisos asignados.') }}</p>
                    @else
                        <ul class="list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300 text-sm">
                            @foreach($userPermissions as $permission)
                                <li>{{ $permission->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-creator-layout>
