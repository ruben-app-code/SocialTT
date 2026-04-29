@push('scripts')
<script>
(function () {
    var networkSelect = document.getElementById('social_network_id');
    var usernameInput = document.getElementById('username');
    var urlPreview = document.getElementById('profile_url_preview');
    if (!networkSelect || !usernameInput || !urlPreview) return;
    var endpoint = @json(route('social-accounts.profile-url-preview'));
    var timer;
    function refresh() {
        clearTimeout(timer);
        timer = setTimeout(function () {
            var params = new URLSearchParams({
                social_network_id: networkSelect.value || '',
                username: usernameInput.value || ''
            });
            fetch(endpoint + '?' + params.toString(), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (r) { return r.ok ? r.json() : Promise.reject(); })
                .then(function (data) { urlPreview.value = data.url || ''; })
                .catch(function () {});
        }, 200);
    }
    networkSelect.addEventListener('change', refresh);
    usernameInput.addEventListener('input', refresh);
})();
</script>
@endpush
