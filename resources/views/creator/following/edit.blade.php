<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('following.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200" title="{{ __('Volver') }}">
                <x-feather-icon name="chevron-left" class="w-5 h-5" />
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Editar entrada') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                @if ($entry->isExternal())
                    @php
                        $usernameDisplay = \App\Support\TikTokFollowingInput::formFieldValue(old('username'), $entry->username);
                        $handlePreview = \App\Support\TikTokFollowingInput::normalizeToHandle($usernameDisplay);
                        if ($handlePreview === null && filled($entry->username)) {
                            $handlePreview = ltrim((string) $entry->username, '@');
                        }
                        $previewUrl = $handlePreview
                            ? \App\Models\SocialNetwork::profileUrlForSlug($tiktokNetwork->slug, (string) $handlePreview)
                            : '';
                    @endphp
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Pega el enlace del perfil o el @usuario. Si la cuenta existe, nombre, foto y seguidores se actualizan solos. Las notas las editas tú.') }}</p>

                    @if (filled($entry->avatar_url))
                        <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/40">
                            <img src="{{ $entry->avatar_url }}" alt="" class="w-14 h-14 rounded-full object-cover border border-gray-200 dark:border-gray-600 shrink-0" width="56" height="56" loading="lazy" referrerpolicy="no-referrer">
                            <div class="min-w-0 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                @if (filled($entry->remote_display_name))
                                    <p><span class="font-medium text-gray-700 dark:text-gray-300">{{ __('Nombre en TikTok') }}:</span> {{ $entry->remote_display_name }}</p>
                                @endif
                                <p>{{ __('Foto desde TikTok (CDN).') }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="post" action="{{ route('following.update', $entry) }}" class="space-y-5" id="following-edit-external-form">
                        @csrf
                        @method('PUT')
                        <div>
                            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Red social') }}</span>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $tiktokNetwork->name }}</p>
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Enlace del perfil o usuario') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="username" id="username" value="{{ $usernameDisplay }}" required maxlength="2048" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="@usuario (pega enlace si quieres; al salir del campo queda @usuario)" autocomplete="off">
                            @error('username')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="profile_url_preview" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Enlace del perfil (generado)') }}</label>
                            <input type="text" id="profile_url_preview" readonly tabindex="-1" value="{{ $previewUrl }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm bg-gray-50 dark:bg-gray-950/50 text-sm cursor-default">
                        </div>
                        <div>
                            <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Título (opcional)') }}</label>
                            <input type="text" name="label" id="label" value="{{ old('label', $entry->label) }}" maxlength="255" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Cómo quieres verlo en tu lista') }}">
                            @error('label')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nota (opcional)') }}</label>
                            <textarea name="note" id="note" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Solo tú la ves') }}">{{ old('note', $entry->note) }}</textarea>
                            @error('note')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50/50 dark:bg-gray-900/30">
                            <input type="checkbox" name="use_custom_avatar" id="use_custom_avatar_ext" value="1" @checked(old('use_custom_avatar', $entry->use_custom_avatar)) class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900">
                            <label for="use_custom_avatar_ext" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <span class="font-medium">{{ __('Foto personalizada') }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Desmarcada: cada vez que guardes o abras esta pantalla se actualizará la imagen desde TikTok. Marcada: se conserva la URL de foto que tengas guardada.') }}</span>
                            </label>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500">{{ __('Guardar cambios') }}</button>
                            <a href="{{ route('following.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
                        </div>
                    </form>

                    @push('scripts')
                    <script>
                    (function () {
                        var previewUrl = @json(route('social-accounts.profile-url-preview'));
                        var networkId = @json((string) $tiktokNetwork->id);
                        var user = document.getElementById('username');
                        var out = document.getElementById('profile_url_preview');
                        if (!user || !out) return;
                        function debounce(fn, ms) {
                            var t;
                            return function () {
                                clearTimeout(t);
                                var a = arguments;
                                t = setTimeout(function () { fn.apply(null, a); }, ms);
                            };
                        }
                        function handleForPreview(raw) {
                            raw = (raw || '').trim();
                            if (!raw) return '';
                            var m = raw.toLowerCase().match(/tiktok\.com\/@([a-z0-9._]+)/i);
                            if (m) return m[1];
                            return raw.replace(/^@/, '').toLowerCase();
                        }
                        function normalizeInputToAtHandle(el) {
                            var v = (el.value || '').trim();
                            if (!v) return;
                            var lower = v.toLowerCase();
                            var m = lower.match(/tiktok\.com\/@([a-z0-9._]+)/i);
                            if (m) { el.value = '@' + m[1]; return; }
                            if (/^https?:\/\//i.test(v) && lower.indexOf('tiktok.com') !== -1) {
                                m = v.match(/@([a-zA-Z0-9._]+)/);
                                if (m) el.value = '@' + m[1].toLowerCase();
                            } else if (/^@?[a-zA-Z0-9._]+$/.test(v.replace(/^@/, ''))) {
                                el.value = '@' + v.replace(/^@/, '').toLowerCase();
                            }
                        }
                        function refresh() {
                            var u = handleForPreview(user.value);
                            var url = previewUrl + '?social_network_id=' + encodeURIComponent(networkId) + '&username=' + encodeURIComponent(u);
                            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                                .then(function (r) { return r.json(); })
                                .then(function (data) { out.value = data.url || ''; })
                                .catch(function () { out.value = ''; });
                        }
                        var run = debounce(refresh, 200);
                        user.addEventListener('input', run);
                        user.addEventListener('blur', function () { normalizeInputToAtHandle(user); refresh(); });
                        refresh();
                    })();
                    </script>
                    @endpush
                @else
                    @php($creator = $entry->platformUser)
                    <div class="flex items-start gap-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-600">
                        @if ($creator)
                            <img src="{{ filled($entry->avatar_url) ? $entry->avatar_url : $creator->avatar_url }}" alt="" class="w-14 h-14 rounded-full object-cover border border-gray-200 dark:border-gray-600 shrink-0">
                            <div class="min-w-0">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Creador en la plataforma') }}</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $creator->name }}</p>
                                @if (filled($entry->remote_display_name))
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('En TikTok') }}: {{ $entry->remote_display_name }}</p>
                                @endif
                                <a href="{{ $creator->creator_profile_url }}" target="_blank" rel="noopener" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline break-all">{{ __('Ver perfil público') }}</a>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Al abrir o guardar se sincronizan enlace, nombre y foto desde TikTok (si aplica), y se guarda el histórico de seguidores.') }}</p>
                            </div>
                        @else
                            <p class="text-sm text-amber-700 dark:text-amber-300">{{ __('El usuario vinculado ya no está disponible. Puedes actualizar la nota o eliminar esta entrada.') }}</p>
                        @endif
                    </div>

                    <form method="post" action="{{ route('following.update', $entry) }}" class="space-y-5">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Título (opcional)') }}</label>
                            <input type="text" name="label" id="label" value="{{ old('label', $entry->label) }}" maxlength="255" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Cómo quieres verlo en tu lista') }}">
                            @error('label')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nota (opcional)') }}</label>
                            <textarea name="note" id="note" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Solo tú la ves') }}">{{ old('note', $entry->note) }}</textarea>
                            @error('note')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50/50 dark:bg-gray-900/30">
                            <input type="checkbox" name="use_custom_avatar" id="use_custom_avatar_plat" value="1" @checked(old('use_custom_avatar', $entry->use_custom_avatar)) class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900">
                            <label for="use_custom_avatar_plat" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                <span class="font-medium">{{ __('Foto personalizada') }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Desmarcada: la imagen se actualiza desde TikTok o, si no hay TikTok, desde el perfil del creador en este sitio. Marcada: no se sustituye la foto guardada al sincronizar.') }}</span>
                            </label>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500">{{ __('Guardar cambios') }}</button>
                            <a href="{{ route('following.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-creator-layout>
