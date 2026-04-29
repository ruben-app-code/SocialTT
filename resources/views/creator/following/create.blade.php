<x-creator-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('following.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200" title="{{ __('Volver') }}">
                <x-feather-icon name="chevron-left" class="w-5 h-5" />
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Añadir cuenta TikTok') }}
            </h2>
        </div>
    </x-slot>

    @php
        $usernameDisplay = \App\Support\TikTokFollowingInput::formFieldValue(old('username'), null);
        $handlePreview = \App\Support\TikTokFollowingInput::normalizeToHandle($usernameDisplay);
        $previewUrl = $handlePreview
            ? \App\Models\SocialNetwork::profileUrlForSlug($tiktokNetwork->slug, $handlePreview)
            : '';
    @endphp

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6 space-y-6">
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('Pega el enlace del perfil (p. ej. tiktok.com/@usuario) o escribe el @usuario. Si la cuenta existe, el nombre, la foto y los seguidores se guardan solos. Solo las notas (y el título opcional) las escribes tú.') }}</p>

                <form method="post" action="{{ route('following.store-external') }}" class="space-y-5" id="following-external-form">
                    @csrf
                    <div>
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Red social') }}</span>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-medium">{{ $tiktokNetwork->name }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Otras redes estarán disponibles más adelante.') }}</p>
                    </div>
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Enlace del perfil o usuario') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="username" id="username" value="{{ $usernameDisplay }}" required maxlength="2048" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="@usuario (puedes pegar el enlace; al salir del campo se deja solo @usuario)" autocomplete="off">
                        @error('username')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="profile_url_preview" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Enlace del perfil (generado)') }}</label>
                        <input type="text" id="profile_url_preview" readonly tabindex="-1" value="{{ $previewUrl }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm bg-gray-50 dark:bg-gray-950/50 text-sm cursor-default">
                    </div>
                    <div>
                        <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Título (opcional)') }}</label>
                        <input type="text" name="label" id="label" value="{{ old('label') }}" maxlength="255" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Cómo quieres verlo en tu lista') }}">
                        @error('label')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nota (opcional)') }}</label>
                        <textarea name="note" id="note" rows="3" maxlength="2000" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Solo tú la ves') }}">{{ old('note') }}</textarea>
                        @error('note')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-start gap-3 rounded-lg border border-gray-200 dark:border-gray-600 p-4 bg-gray-50/50 dark:bg-gray-900/30">
                        <input type="checkbox" name="use_custom_avatar" id="use_custom_avatar" value="1" @checked(old('use_custom_avatar', false)) class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900">
                        <label for="use_custom_avatar" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                            <span class="font-medium">{{ __('Foto personalizada') }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Desactivada por defecto: se guarda la foto actual de TikTok. Si la activas, no se cambiará la imagen al sincronizar (puedes dejarla vacía o gestionarla aparte).') }}</span>
                        </label>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-500">{{ __('Guardar en mi lista') }}</button>
                        <a href="{{ route('following.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm hover:bg-gray-50 dark:hover:bg-gray-700">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
</x-creator-layout>
