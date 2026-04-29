<x-administrator-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar usuario') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">{{ session('success') }}</div>
            @endif

            @php $errSocialAccId = session('edit_social_account_error_id'); @endphp

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('New Password') }}</label>
                                <input type="password" name="password" id="password" autocomplete="new-password"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="{{ __('Leave blank to keep current') }}">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                                    class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            @php
                                $lockActive = $user->id === auth()->id() || $user->isSuperAdmin();
                            @endphp
                            <div class="user-active-field" id="user-active-field-edit">
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Activo') }}</span>
                                <label class="switch" id="user-active-switch-label" title="@if($user->id === auth()->id()){{ __('No puede desactivar su propia cuenta desde aquí.') }}@elseif($user->isSuperAdmin()){{ __('Quite el rol SuperAdmin para poder desactivar.') }}@else{{ $user->active ? __('Activo') : __('Inactivo') }}@endif">
                                    <input
                                        type="checkbox"
                                        name="active"
                                        id="user-active-input"
                                        value="1"
                                        {{ old('active', $user->active) ? 'checked' : '' }}
                                        @if($lockActive) disabled @endif
                                    >
                                    <span class="slider round"></span>
                                </label>
                                @if($lockActive)
                                    <input type="hidden" name="active" value="1" id="user-active-lock-hidden">
                                @endif
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Si está desactivado, no podrá iniciar sesión hasta que un administrador lo active.') }}</p>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Roles') }}</span>
                                <div class="space-y-2">
                                    @foreach($roles as $role)
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" data-is-admin-role="{{ $role->name === \App\Models\User::SUPERADMIN_ROLE ? '1' : '0' }}" class="js-role-checkbox rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700"
                                                {{ $user->roles->contains('id', $role->id) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $role->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                    {{ __('Save') }}
                                </button>
                                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Cuentas sociales') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Registros en social_accounts vinculados a este usuario (user_id).') }}</p>
                </div>
                <div class="p-6 space-y-8">
                    @forelse($user->socialAccounts as $account)
                        @php
                            $tz = config('app.timezone');
                            $block = $account->block;
                            $showErrorsHere = (int) $errSocialAccId === (int) $account->id;
                            if ($showErrorsHere) {
                                $blockedAtValue = old('blocked_at');
                                if ($blockedAtValue === null && $block?->blocked_at) {
                                    $blockedAtValue = $block->blocked_at->timezone($tz)->format('Y-m-d\TH:i');
                                }
                            } elseif ($block?->blocked_at) {
                                $blockedAtValue = $block->blocked_at->timezone($tz)->format('Y-m-d\TH:i');
                            } else {
                                $blockedAtValue = '';
                            }
                            $defaultBlockH = (int) config('social_accounts.default_block_duration_hours', 24);
                            if ($showErrorsHere) {
                                $durationValue = old('block_duration_hours');
                            } else {
                                $durationValue = null;
                            }
                            if ($durationValue === null && $block && $block->blocked_at && $block->activates_at) {
                                $durationValue = max(1, (int) round($block->blocked_at->diffInMinutes($block->activates_at) / 60));
                            }
                            $durationValue = $durationValue !== null && $durationValue !== '' ? (int) $durationValue : $defaultBlockH;
                            $networkIdForSelect = $showErrorsHere ? (int) old('social_network_id', $account->social_network_id) : (int) $account->social_network_id;
                            $displayNameVal = $showErrorsHere ? old('display_name', $account->display_name) : $account->display_name;
                            $usernameVal = $showErrorsHere ? old('username', $account->username) : $account->username;
                            $statusVal = $showErrorsHere ? old('current_status', $account->current_status) : $account->current_status;
                            $primaryVal = $showErrorsHere ? (old('is_primary', $account->is_primary ? '1' : '0') === '1') : $account->is_primary;
                            $verifiedVal = $showErrorsHere ? (old('is_verified', $account->is_verified ? '1' : '0') === '1') : $account->is_verified;
                            $selectedTopicIds = $showErrorsHere
                                ? array_values(array_unique(array_filter(array_map('intval', (array) old('topics', [])))))
                                : $account->topics->pluck('id')->all();
                            $previewNetwork = $socialNetworks->firstWhere('id', $networkIdForSelect);
                            $profileUrlPreview = $previewNetwork
                                ? \App\Models\SocialNetwork::profileUrlForSlug($previewNetwork->slug, (string) $usernameVal)
                                : '';
                        @endphp
                        <div class="rounded-lg border border-gray-200 dark:border-gray-600 p-5 space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Cuenta #:id', ['id' => $account->id]) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Creada') }} {{ $account->created_at?->format('Y-m-d H:i') }} · {{ __('Actualizada') }} {{ $account->updated_at?->format('Y-m-d H:i') }} · {{ __('Temas vinculados') }}: {{ $account->topics_count }}</p>
                                    @if($account->last_checked_at)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Última comprobación') }}: {{ $account->last_checked_at->format('Y-m-d H:i') }}</p>
                                    @endif
                                </div>
                                <form action="{{ route('admin.users.social-accounts.destroy', [$user, $account]) }}" method="POST" class="shrink-0"
                                    onsubmit="return confirm(@json(__('¿Eliminar esta cuenta social? Esta acción no se puede deshacer.')));">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md text-xs font-semibold text-white uppercase tracking-wide hover:bg-red-500">
                                        {{ __('Eliminar cuenta') }}
                                    </button>
                                </form>
                            </div>

                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-gray-600 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 pt-3">
                                <div><dt class="font-medium text-gray-700 dark:text-gray-300 inline">{{ __('URL guardada') }}:</dt> <dd class="inline break-all">@if($account->url)<a href="{{ $account->url }}" class="text-indigo-600 dark:text-indigo-400 underline" target="_blank" rel="noopener">{{ $account->url }}</a>@else—@endif</dd></div>
                                <div><dt class="font-medium text-gray-700 dark:text-gray-300 inline">{{ __('Red (slug)') }}:</dt> <dd class="inline">{{ $account->socialNetwork?->slug ?? '—' }}</dd></div>
                            </dl>

                            <form action="{{ route('admin.users.social-accounts.update', [$user, $account]) }}" method="POST" class="space-y-4 border-t border-gray-100 dark:border-gray-700 pt-4">
                                @csrf
                                @method('PUT')

                                <div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50/50 dark:bg-gray-900/20 p-4 space-y-4">
                                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Red social y datos del perfil') }}</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Red social') }} <span class="text-red-500">*</span></label>
                                        <select name="social_network_id" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            @foreach($socialNetworks as $network)
                                                <option value="{{ $network->id }}" @selected($networkIdForSelect === (int) $network->id)>{{ $network->name }}</option>
                                            @endforeach
                                        </select>
                                        @if($showErrorsHere)
                                            @error('social_network_id')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Nombre para mostrar') }}</label>
                                        <input type="text" name="display_name" value="{{ $displayNameVal }}"
                                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm" maxlength="255">
                                        @if($showErrorsHere)
                                            @error('display_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Usuario') }} <span class="text-red-500">*</span></label>
                                    <input type="text" name="username" value="{{ $usernameVal }}" required maxlength="255"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                    @if($showErrorsHere)
                                        @error('username')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('URL del perfil (vista previa)') }}</label>
                                    <input type="text" readonly value="{{ $profileUrlPreview }}" tabindex="-1"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm bg-gray-50 dark:bg-gray-900/50 text-sm cursor-default">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Se recalcula al guardar según la red y el usuario.') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Estado') }} <span class="text-red-500">*</span></label>
                                    <select name="current_status" required class="w-full max-w-md rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                        <option value="active" @selected($statusVal === 'active')>{{ __('Activa') }}</option>
                                        <option value="blocked" @selected($statusVal === 'blocked')>{{ __('Bloqueada') }}</option>
                                        <option value="deleted" @selected($statusVal === 'deleted')>{{ __('Eliminada') }}</option>
                                        <option value="stolen" @selected($statusVal === 'stolen')>{{ __('Robada') }}</option>
                                    </select>
                                    @if($showErrorsHere)
                                        @error('current_status')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div class="rounded-lg border border-amber-200 dark:border-amber-900/50 bg-amber-50/80 dark:bg-amber-950/20 px-4 py-3 space-y-3">
                                    <p class="text-xs text-amber-900 dark:text-amber-200/90">{{ __('Bloqueo temporal: solo se guarda en base de datos si el estado es «Bloqueada».') }}</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Inicio del bloqueo') }}</label>
                                            <input type="datetime-local" name="blocked_at" value="{{ $blockedAtValue }}"
                                                class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Vacío = ahora. Zona: :tz', ['tz' => $tz]) }}</p>
                                            @if($showErrorsHere)
                                                @error('blocked_at')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                            @endif
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Duración (horas)') }}</label>
                                            <input type="number" name="block_duration_hours" value="{{ $durationValue }}" min="1" max="8760" step="1"
                                                class="mt-1 w-full max-w-xs rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                            @if($showErrorsHere)
                                                @error('block_duration_hours')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-8">
                                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                        <input type="hidden" name="is_verified" value="0">
                                        <input type="checkbox" name="is_verified" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600" @checked($verifiedVal)>
                                        <span>{{ __('Cuenta verificada') }}</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                        <input type="hidden" name="is_primary" value="0">
                                        <input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600" @checked($primaryVal)>
                                        <span>{{ __('Cuenta principal en esta red') }}</span>
                                    </label>
                                </div>
                                @if($showErrorsHere)
                                    @error('is_verified')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                    @error('is_primary')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                @endif

                                </div>{{-- / Red social y perfil --}}

                                <div class="rounded-xl border-2 border-indigo-200 dark:border-indigo-800/70 bg-indigo-50/40 dark:bg-indigo-950/25 p-4 space-y-3 shadow-sm" data-admin-account-topics-panel>
                                    <div>
                                        <h4 class="text-sm font-semibold text-indigo-950 dark:text-indigo-100">{{ __('Temas de esta cuenta') }}</h4>
                                        <p class="mt-1 text-xs text-indigo-900/75 dark:text-indigo-200/80 leading-relaxed">{{ __('Clasificación de contenido vinculada a la cuenta social (pivot social_account_topic). No confundir con la red elegida arriba: aquí solo defines a qué temas pertenece esta cuenta.') }}</p>
                                    </div>
                                    @if($topicsForAccounts->isEmpty())
                                        <p class="text-sm text-amber-800 dark:text-amber-200">{{ __('No hay temas en el sistema. Créalos en el administrador de temas.') }}</p>
                                    @else
                                        <div>
                                            <label for="admin-topic-filter-{{ $account->id }}" class="block text-xs font-medium text-indigo-900 dark:text-indigo-200 mb-1">{{ __('Filtrar por texto') }}</label>
                                            <input type="search" id="admin-topic-filter-{{ $account->id }}" class="js-admin-topic-filter w-full rounded-md border-indigo-200 dark:border-indigo-800 dark:bg-gray-800 dark:text-white shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Escribe para acotar la lista…') }}" autocomplete="off">
                                        </div>
                                        <div class="max-h-52 overflow-y-auto rounded-lg border border-indigo-200/80 dark:border-indigo-800/80 bg-white/80 dark:bg-gray-900/50 p-3 space-y-2">
                                            @foreach($topicsForAccounts as $topic)
                                                <label class="js-admin-topic-row flex items-start gap-2 cursor-pointer text-sm text-gray-800 dark:text-gray-200"
                                                    data-topic-search-text="{{ e(mb_strtolower($topic->display_name, 'UTF-8')) }}">
                                                    <input type="checkbox" name="topics[]" value="{{ $topic->id }}" class="mt-0.5 rounded border-gray-300 dark:border-gray-600 text-indigo-600"
                                                        @checked(in_array((int) $topic->id, $selectedTopicIds, true))>
                                                    <span>{{ $topic->display_name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($showErrorsHere)
                                        @error('topics')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                        @error('topics.*')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                    @endif
                                </div>

                                <div>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                        {{ __('Guardar cuenta social') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Este usuario no tiene cuentas sociales registradas.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <script>
    (function () {
        document.querySelectorAll('.js-admin-topic-filter').forEach(function (input) {
            var panel = input.closest('[data-admin-account-topics-panel]');
            if (!panel) return;
            var rows = panel.querySelectorAll('.js-admin-topic-row');
            function applyFilter() {
                var q = (input.value || '').trim().toLowerCase();
                rows.forEach(function (row) {
                    var t = (row.getAttribute('data-topic-search-text') || '').toLowerCase();
                    row.style.display = (!q || t.indexOf(q) !== -1) ? '' : 'none';
                });
            }
            input.addEventListener('input', applyFilter);
            input.addEventListener('search', applyFilter);
        });
    })();
    </script>
    @if($user->isSuperAdmin() && $user->id !== auth()->id())
    <script>
    (function () {
        var adminCb = document.querySelector('input.js-role-checkbox[data-is-admin-role="1"]');
        var activeInput = document.getElementById('user-active-input');
        var hidden = document.getElementById('user-active-lock-hidden');
        function syncActiveLock() {
            if (!adminCb || !activeInput) return;
            if (adminCb.checked) {
                activeInput.checked = true;
                activeInput.disabled = true;
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'active';
                    hidden.value = '1';
                    hidden.id = 'user-active-lock-hidden';
                    document.getElementById('user-active-field-edit').appendChild(hidden);
                }
            } else {
                activeInput.disabled = false;
                if (hidden) { hidden.remove(); hidden = null; }
            }
        }
        if (adminCb) adminCb.addEventListener('change', syncActiveLock);
    })();
    </script>
    @endif
</x-administrator-layout>
