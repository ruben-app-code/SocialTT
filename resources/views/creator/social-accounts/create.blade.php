<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('social-accounts.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <x-feather-icon name="chevron-left" class="w-5 h-5" />
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Añadir red social') }}
            </h2>
        </div>
    </x-slot>

    @php
        $initialNetwork = $socialNetworks->firstWhere('id', (int) old('social_network_id', 0));
        $initialUsername = old('username', '');
        $profileUrlPreview = $initialNetwork
            ? \App\Models\SocialNetwork::profileUrlForSlug($initialNetwork->slug, $initialUsername)
            : '';
    @endphp

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('social-accounts.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf

                    <div>
                        <label for="social_network_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Red social') }}</label>
                        <select name="social_network_id" id="social_network_id" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Selecciona...') }}</option>
                            @foreach ($socialNetworks as $network)
                                <option value="{{ $network->id }}" data-slug="{{ $network->slug }}" @selected(old('social_network_id') == $network->id)>{{ $network->name }}</option>
                            @endforeach
                        </select>
                        @error('social_network_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nombre') }}</label>
                        <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}" maxlength="255" placeholder="{{ __('Nombre para identificar esta cuenta') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Usuario') }}</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required maxlength="255" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="profile_url_preview" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('URL del perfil') }}</label>
                        <input type="text" id="profile_url_preview" readonly value="{{ $profileUrlPreview }}" tabindex="-1"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm bg-gray-50 dark:bg-gray-900/50 cursor-default text-gray-700 dark:text-gray-300">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Se genera automáticamente según la red y el usuario.') }}</p>
                    </div>

                    @include('creator.social-accounts.partials.verify-profile')

                    @include('creator.social-accounts.partials.status-and-block-fields', ['defaultStatus' => old('current_status', 'active')])

                    <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-900/40 px-4 py-3">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Cuenta verificada') }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Las cuentas nuevas empiezan sin verificar. Podrás marcarla en el listado de redes cuando confirmes que es la cuenta correcta en la plataforma.') }}</p>
                    </div>

                    @php
                        $isPrimaryValue = (string) old('is_primary', '0');
                        if (!in_array($isPrimaryValue, ['0', '1'], true)) {
                            $isPrimaryValue = '0';
                        }
                        $isPrimaryChecked = $isPrimaryValue === '1';
                    @endphp
                    @include('creator.social-accounts.partials.is-primary-field', [
                        'mode' => 'create',
                        'accountsPerNetwork' => $accountsPerNetwork,
                        'initialNetworkId' => (string) old('social_network_id', ''),
                        'isPrimaryValue' => $isPrimaryValue,
                        'isPrimaryChecked' => $isPrimaryChecked,
                    ])

                    @include('creator.social-accounts.partials.topic-fields', [
                        'mode' => 'create',
                        'selectedTopics' => $selectedTopics,
                    ])

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Guardar') }}
                        </button>
                        <a href="{{ route('social-accounts.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('creator.social-accounts.partials.profile-url-preview-script')
</x-creator-layout>
