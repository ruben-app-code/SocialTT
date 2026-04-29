@props([
    'mode' => 'create',
    'accountsPerNetwork',
    'initialNetworkId' => '',
    'isPrimaryValue' => '0',
    'isPrimaryChecked' => false,
])
<div id="is-primary-wrap" class="flex items-start gap-3">
    <input type="hidden" name="is_primary" id="is_primary_value" value="{{ $isPrimaryValue }}">
    <input type="checkbox" id="is_primary_cb"
        class="mt-0.5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500"
        @checked($isPrimaryChecked)>
    <div>
        <label for="is_primary_cb" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Cuenta principal de esta red') }}</label>
        <p id="is-primary-hint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
    </div>
</div>
@push('scripts')
<script>
(function () {
    var accountsPerNetwork = @json($accountsPerNetwork);
    var mode = @json($mode);
    var initialNetworkId = @json((string) $initialNetworkId);
    var $select = document.getElementById('social_network_id');
    var $wrap = document.getElementById('is-primary-wrap');
    var $cb = document.getElementById('is_primary_cb');
    var $hidden = document.getElementById('is_primary_value');
    var $hint = document.getElementById('is-primary-hint');
    if (!$select || !$cb || !$hidden) {
        return;
    }

    var firstSync = true;

    function othersExcludingEditing(selectedNetworkId) {
        var total = accountsPerNetwork[selectedNetworkId] || accountsPerNetwork[String(selectedNetworkId)] || 0;
        if (mode === 'create') {
            return total;
        }
        if (String(selectedNetworkId) === String(initialNetworkId)) {
            return Math.max(0, total - 1);
        }
        return total;
    }

    function sync() {
        var sel = $select.value;
        if (!sel) {
            $wrap.classList.add('opacity-50', 'pointer-events-none');
            $hint.textContent = '';
            return;
        }
        $wrap.classList.remove('opacity-50', 'pointer-events-none');
        var others = othersExcludingEditing(sel);
        if (others === 0) {
            $cb.checked = true;
            $cb.disabled = true;
            $hidden.value = '1';
            $hint.textContent = @json(__('Primera cuenta de esta red: se guardará como principal.'));
        } else {
            $cb.disabled = false;
            if (!firstSync) {
                $cb.checked = false;
                $hidden.value = '0';
            } else {
                $hidden.value = $cb.checked ? '1' : '0';
            }
            $hint.textContent = @json(__('Solo una cuenta puede ser principal por red. Si la marcas, las demás dejarán de serlo.'));
        }
        firstSync = false;
    }

    $cb.addEventListener('change', function () {
        if ($cb.disabled) {
            return;
        }
        $hidden.value = $cb.checked ? '1' : '0';
    });

    $select.addEventListener('change', sync);
    sync();
})();
</script>
@endpush
