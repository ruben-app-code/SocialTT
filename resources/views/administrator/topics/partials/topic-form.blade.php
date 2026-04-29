@php
    $isEdit = isset($topic) && $topic !== null;
    $hasChildren = $isEdit && ($topic->children_count ?? 0) > 0;
    $defaultKind = old('kind', $isEdit ? ($topic->parent_id ? 'sub' : 'root') : 'root');
    $defaultParentId = old('parent_id', $isEdit ? $topic->parent_id : null);
    $defaultParentLabel = old('parent_label');
    if ($defaultParentLabel === null && $isEdit && $topic->parent) {
        $defaultParentLabel = $topic->parent->name;
    }
@endphp

<div class="space-y-6">
    <div>
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Tipo') }}</span>
        <div class="flex flex-col sm:flex-row gap-4">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="kind" value="root" class="rounded-full border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" {{ $defaultKind === 'root' ? 'checked' : '' }} {{ $hasChildren ? 'checked disabled' : '' }}>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Tema principal') }}</span>
            </label>
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="radio" name="kind" value="sub" class="rounded-full border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 topic-kind-sub" {{ $defaultKind === 'sub' ? 'checked' : '' }} {{ $hasChildren ? 'disabled' : '' }}>
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Subtema') }} <span class="text-xs text-gray-500">({{ __('depende de un tema principal') }})</span></span>
            </label>
        </div>
        @if ($hasChildren)
            <input type="hidden" name="kind" value="root">
            <p class="mt-2 text-xs text-amber-700 dark:text-amber-300">{{ __('Este tema tiene subtemas: debe seguir siendo principal.') }}</p>
        @endif
        @error('kind')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="topic-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nombre') }}</label>
        <input type="text" name="name" id="topic-name" value="{{ old('name', $isEdit ? $topic->name : '') }}" required maxlength="255"
            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="topic-slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Slug') }} <span class="text-xs font-normal text-gray-500">({{ __('opcional; se genera del nombre si lo dejas vacío') }})</span></label>
        <input type="text" name="slug" id="topic-slug" value="{{ old('slug', $isEdit ? $topic->slug : '') }}" maxlength="255"
            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm font-mono text-sm">
        @error('slug')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div id="topic-parent-section" class="{{ $defaultKind === 'sub' && !$hasChildren ? '' : 'hidden' }} space-y-3 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50/50 dark:bg-gray-900/20">
        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tema principal (padre)') }}</span>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Escribe al menos 3 letras para buscar entre los temas principales; elige uno de la lista.') }}</p>

        <input type="hidden" name="parent_id" id="topic_parent_id" value="{{ $defaultParentId }}">

        <div id="topic-parent-selected" class="{{ $defaultParentId && $defaultKind === 'sub' ? '' : 'hidden' }} flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Seleccionado:') }}</span>
            <span id="topic-parent-chip" class="inline-flex items-center gap-1 pl-2.5 pr-1 py-1 rounded-lg text-sm bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-100 border border-indigo-200 dark:border-indigo-700">
                <span id="topic-parent-chip-label">{{ $defaultParentLabel }}</span>
                <button type="button" id="topic-parent-clear" class="rounded p-0.5 text-indigo-600 hover:bg-indigo-100 dark:hover:bg-indigo-800" title="{{ __('Quitar') }}">&times;</button>
            </span>
        </div>

        <div>
            <label for="topic-parent-search" class="sr-only">{{ __('Buscar tema principal') }}</label>
            <input type="search" id="topic-parent-search" autocomplete="off" placeholder="{{ __('Mínimo 3 caracteres…') }}"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>

        <div id="topic-parent-panel" class="hidden rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 overflow-hidden">
            <div id="topic-parent-loader" class="hidden items-center gap-2 px-3 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/40">
                <span class="inline-block size-4 shrink-0 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin" aria-hidden="true"></span>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Buscando…') }}</span>
            </div>
            <div id="topic-parent-results" class="divide-y divide-gray-100 dark:divide-gray-700 max-h-48 overflow-y-auto"></div>
        </div>
        @error('parent_id')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function () {
    var searchUrl = @json(route('admin.topics.search'));
    var excludeId = @json($isEdit ? $topic->id : null);
    var $kindSub = $('.topic-kind-sub');
    var $kindRoot = $('input[name="kind"][value="root"]');
    var $section = $('#topic-parent-section');
    var $search = $('#topic-parent-search');
    var $panel = $('#topic-parent-panel');
    var $results = $('#topic-parent-results');
    var $loader = $('#topic-parent-loader');
    var $hidden = $('#topic_parent_id');
    var $selectedWrap = $('#topic-parent-selected');
    var $chipLabel = $('#topic-parent-chip-label');
    var timer = null;
    var seq = 0;
    var debounceMs = 280;
    var pointerOnHit = false;

    function isSubMode() {
        var $sub = $('input[name="kind"][value="sub"]:not(:disabled)');
        return $sub.length && $sub.is(':checked');
    }

    function toggleSection() {
        if (isSubMode()) {
            $section.removeClass('hidden');
        } else {
            $section.addClass('hidden');
            $hidden.val('');
            $selectedWrap.addClass('hidden');
            $panel.addClass('hidden');
            $results.empty();
        }
    }

    $('input[name="kind"]').on('change', toggleSection);
    toggleSection();

    function setLoading(on) {
        if (on) {
            $loader.removeClass('hidden').addClass('flex');
        } else {
            $loader.addClass('hidden').removeClass('flex');
        }
    }

    function whenSafe(fn) {
        var start = Date.now();
        function tick() {
            if (!pointerOnHit) {
                fn();
                return;
            }
            if (Date.now() - start > 2000) {
                pointerOnHit = false;
                fn();
                return;
            }
            setTimeout(tick, 16);
        }
        tick();
    }

    function renderRows(rows) {
        $results.empty();
        if (!rows || !rows.length) {
            $panel.addClass('hidden');
            return;
        }
        rows.forEach(function (row) {
            var $btn = $('<button type="button" class="topic-parent-hit block w-full text-left px-3 py-2 text-sm text-indigo-700 dark:text-indigo-300 hover:bg-gray-50 dark:hover:bg-gray-700 border-0 bg-transparent cursor-pointer"></button>');
            $btn.attr('data-id', row.id);
            $btn.attr('data-name', row.name);
            $btn.text(row.name);
            $results.append($btn);
        });
        $panel.removeClass('hidden');
    }

    function runSearch() {
        var q = ($search.val() || '').trim();
        if (q.length < 3) {
            seq++;
            setLoading(false);
            $results.empty();
            $panel.addClass('hidden');
            return;
        }
        var mySeq = ++seq;
        setLoading(true);
        $panel.removeClass('hidden');
        var params = { q: q, roots_only: 1 };
        if (excludeId) {
            params.exclude_id = excludeId;
        }
        $.getJSON(searchUrl, params)
            .done(function (data) {
                if (mySeq !== seq) return;
                setLoading(false);
                whenSafe(function () {
                    if (mySeq !== seq) return;
                    renderRows(data.topics || []);
                });
            })
            .fail(function () {
                if (mySeq !== seq) return;
                setLoading(false);
                $results.empty();
                $panel.addClass('hidden');
            });
    }

    $search.on('input', function () {
        clearTimeout(timer);
        var q = ($search.val() || '').trim();
        if (q.length < 3) {
            seq++;
            setLoading(false);
            $results.empty();
            $panel.addClass('hidden');
            return;
        }
        timer = setTimeout(runSearch, debounceMs);
    });

    $search.on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(timer);
            runSearch();
        }
    });

    $results.on('pointerdown', '.topic-parent-hit', function () {
        pointerOnHit = true;
    });
    $(document).on('pointerup pointercancel', function () {
        pointerOnHit = false;
    });

    $results.on('mousedown', '.topic-parent-hit', function (e) {
        e.preventDefault();
    });

    $results.on('click', '.topic-parent-hit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        var name = $(this).data('name');
        $hidden.val(id);
        $chipLabel.text(name);
        $selectedWrap.removeClass('hidden');
        setTimeout(function () {
            $(e.target).closest('.topic-parent-hit').remove();
            if (!$results.children().length) {
                $panel.addClass('hidden');
            }
        }, 0);
    });

    $('#topic-parent-clear').on('click', function () {
        $hidden.val('');
        $selectedWrap.addClass('hidden');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#topic-parent-panel, #topic-parent-search').length) {
            $panel.addClass('hidden');
        }
    });
})();
</script>
@endpush
