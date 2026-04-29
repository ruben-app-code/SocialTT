@php
    $defaultStatus = $defaultStatus ?? 'active';
    $statusValue = old('current_status', $defaultStatus);
    $defaultBlockH = (int) config('social_accounts.default_block_duration_hours', 24);
    $block = isset($socialAccount) ? $socialAccount->block : null;
    $tz = config('app.timezone');
    $blockedAtValue = old('blocked_at');
    if ($blockedAtValue === null && $block?->blocked_at) {
        $blockedAtValue = $block->blocked_at->timezone($tz)->format('Y-m-d\TH:i');
    }
    $durationValue = old('block_duration_hours');
    if ($durationValue === null && $block && $block->blocked_at && $block->activates_at) {
        $durationValue = max(1, (int) round($block->blocked_at->diffInMinutes($block->activates_at) / 60));
    }
    $durationValue = $durationValue !== null ? (int) $durationValue : $defaultBlockH;
    $showBlockFields = $statusValue === 'blocked';
@endphp
<fieldset>
    <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Estado') }}</legend>
    <div class="mt-2 space-y-2" role="radiogroup" aria-label="{{ __('Estado') }}">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="current_status" value="active" @checked($statusValue === 'active') required
                class="js-status-radio border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Activa') }}</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="current_status" value="blocked" @checked($statusValue === 'blocked')
                class="js-status-radio border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Bloqueada') }}</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="current_status" value="deleted" @checked($statusValue === 'deleted')
                class="js-status-radio border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Eliminada') }}</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" name="current_status" value="stolen" @checked($statusValue === 'stolen')
                class="js-status-radio border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-700 dark:focus:ring-offset-gray-800">
            <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Robada') }}</span>
        </label>
    </div>
    @error('current_status')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <div id="social-account-block-fields" class="mt-4 rounded-lg border border-amber-200 dark:border-amber-900/50 bg-amber-50/80 dark:bg-amber-950/20 px-4 py-3 space-y-3 @if(! $showBlockFields) hidden @endif">
        <p class="text-xs text-amber-900 dark:text-amber-200/90">{{ __('Se guarda en una tabla de bloqueos: hora del bloqueo y próxima reactivación (bloqueo + duración en horas).') }}</p>
        <div>
            <label for="blocked_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Inicio del bloqueo') }}</label>
            <input type="datetime-local" name="blocked_at" id="blocked_at" value="{{ $blockedAtValue }}"
                class="mt-1 block w-full max-w-md rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Vacío = ahora. Zona: :tz', ['tz' => $tz]) }}</p>
            @error('blocked_at')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="block_duration_hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Duración (horas)') }}</label>
            <input type="number" name="block_duration_hours" id="block_duration_hours" value="{{ $durationValue }}" min="1" max="8760" step="1"
                class="mt-1 block w-full max-w-xs rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('La reactivación prevista = inicio + estas horas.') }}</p>
            @error('block_duration_hours')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    </div>
</fieldset>
<script>
document.querySelectorAll('.js-status-radio').forEach(function (radio) {
    radio.addEventListener('change', function () {
        var panel = document.getElementById('social-account-block-fields');
        if (!panel) return;
        var blocked = document.querySelector('input.js-status-radio[name="current_status"][value="blocked"]');
        if (blocked && blocked.checked) {
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    });
});
</script>
