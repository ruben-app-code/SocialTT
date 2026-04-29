<div class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-slate-900/40 px-4 py-4">
    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('Comprobar perfil público') }}</p>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Hacemos una petición GET a la URL del perfil. Intentamos leer seguidores y seguidos (cuando la red los expone en el HTML). El resultado es orientativo.') }}</p>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <button type="button" id="btn-verify-social-profile" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-800 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
            <x-feather-icon name="globe" class="w-4 h-4" />
            {{ __('Verificar ahora') }}
        </button>
        <span id="verify-social-profile-loading" class="hidden text-sm text-gray-500 dark:text-gray-400">{{ __('Comprobando…') }}</span>
    </div>
    <div id="verify-social-profile-result" class="mt-3 hidden rounded-lg border px-3 py-2 text-sm" role="status"></div>
</div>

@push('scripts')
<script>
(function () {
    var btn = document.getElementById('btn-verify-social-profile');
    var netEl = document.getElementById('social_network_id');
    var userEl = document.getElementById('username');
    var loading = document.getElementById('verify-social-profile-loading');
    var box = document.getElementById('verify-social-profile-result');
    var endpoint = @json(route('social-accounts.verificar'));
    if (!btn || !netEl || !userEl || !box) return;
    btn.addEventListener('click', function () {
        var net = netEl.value;
        var user = (userEl.value || '').trim();
        if (!net || !user) {
            alert(@json(__('Elige una red e indica el usuario.')));
            return;
        }
        box.classList.add('hidden');
        box.textContent = '';
        loading.classList.remove('hidden');
        btn.disabled = true;
        var url = endpoint + '?social_network_id=' + encodeURIComponent(net) + '&username=' + encodeURIComponent(user);
        fetch(url, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) {
                return r.json().then(function (body) {
                    if (!r.ok) {
                        if (r.status === 422 && body.errors) {
                            throw body;
                        }
                        throw new Error(body.message || ('HTTP ' + r.status));
                    }
                    return body;
                });
            })
            .then(function (data) {
                var parts = [];
                if (data.message) parts.push(data.message);
                if (data.http_status != null) parts.push('HTTP ' + data.http_status);
                if (data.url) parts.push(data.url);
                if (data.followers != null) {
                    parts.push(@json(__('Seguidores (aprox.):')) + ' ' + new Intl.NumberFormat().format(data.followers));
                } else if (data.followers_raw) {
                    parts.push(@json(__('Seguidores (texto):')) + ' ' + data.followers_raw);
                }
                if (data.following != null) {
                    parts.push(@json(__('Siguiendo (aprox.):')) + ' ' + new Intl.NumberFormat().format(data.following));
                } else if (data.following_raw) {
                    parts.push(@json(__('Siguiendo (texto):')) + ' ' + data.following_raw);
                }
                if (data.login_wall) parts.push(@json(__('Pantalla de acceso detectada.')));
                box.textContent = parts.join(' · ');
                box.classList.remove('hidden', 'border-red-300', 'bg-red-50', 'text-red-800', 'dark:border-red-800', 'dark:bg-red-950/40', 'dark:text-red-200', 'border-emerald-300', 'bg-emerald-50', 'text-emerald-900', 'dark:border-emerald-800', 'dark:bg-emerald-950/30', 'dark:text-emerald-200', 'border-amber-300', 'bg-amber-50', 'text-amber-900', 'dark:border-amber-800', 'dark:bg-amber-950/30', 'dark:text-amber-200');
                if (data.reachable && (data.followers != null || data.following != null)) {
                    box.classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-900', 'dark:border-emerald-800', 'dark:bg-emerald-950/30', 'dark:text-emerald-200');
                } else if (data.reachable) {
                    box.classList.add('border-amber-300', 'bg-amber-50', 'text-amber-900', 'dark:border-amber-800', 'dark:bg-amber-950/30', 'dark:text-amber-200');
                } else {
                    box.classList.add('border-red-300', 'bg-red-50', 'text-red-800', 'dark:border-red-800', 'dark:bg-red-950/40', 'dark:text-red-200');
                }
            })
            .catch(function (err) {
                var msg = @json(__('No se pudo verificar.'));
                if (err && err.errors) {
                    msg = Object.values(err.errors).flat().join(' ');
                } else if (err && err.message) {
                    msg = err.message;
                }
                box.textContent = msg;
                box.classList.remove('hidden');
                box.className = 'mt-3 rounded-lg border px-3 py-2 text-sm border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-950/40 dark:text-red-200';
            })
            .finally(function () {
                loading.classList.add('hidden');
                btn.disabled = false;
            });
    });
})();
</script>
@endpush
