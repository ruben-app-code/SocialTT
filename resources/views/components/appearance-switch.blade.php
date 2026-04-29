@props([
    'variant' => 'default',
    'compact' => false,
])

@php
    $isDark = $appearanceIsDark ?? false;
    $labelClass = $variant === 'guest'
        ? 'text-[10px] uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]'
        : 'text-[10px] uppercase tracking-wide text-gray-500 dark:text-gray-400';
@endphp

<div {{ $attributes->merge(['class' => 'theme-appearance-field inline-flex items-center gap-2 shrink-0']) }}>
    @unless($compact)
        <span class="{{ $labelClass }}">{{ __('Claro') }}</span>
    @endunless
    <label class="switch shrink-0" title="{{ __('Tema oscuro / claro') }}">
        <input
            type="checkbox"
            class="js-appearance-session-toggle"
            {{ $isDark ? 'checked' : '' }}
            aria-label="{{ __('Activar tema oscuro') }}"
        >
        <span class="slider round"></span>
    </label>
    @unless($compact)
        <span class="{{ $labelClass }}">{{ __('Oscuro') }}</span>
    @endunless
</div>

@once
    @push('scripts')
        <script>
        (function () {
            document.querySelectorAll('.js-appearance-session-toggle').forEach(function (input) {
                if (input.dataset.appearanceBound) return;
                input.dataset.appearanceBound = '1';
                input.addEventListener('change', function () {
                    var appearance = this.checked ? 'dark' : 'light';
                    var prevChecked = !this.checked;
                    var el = this;
                    fetch(@json(route('appearance.session')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ appearance: appearance })
                    })
                    .then(function (r) {
                        if (!r.ok) throw new Error();
                        return r.json();
                    })
                    .then(function () {
                        window.location.reload();
                    })
                    .catch(function () {
                        el.checked = prevChecked;
                        alert(@json(__('Error al guardar el tema.')));
                    });
                });
            });
        })();
        </script>
    @endpush
@endonce
