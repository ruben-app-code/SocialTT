@php
    $mode = $mode ?? 'create';
    $selectedTopics = $selectedTopics ?? collect();
    $oldTopicIds = old('topic_ids');
    if (is_array($oldTopicIds)) {
        $initialHidden = implode(',', array_map('intval', $oldTopicIds));
    } else {
        $initialHidden = (string) ($oldTopicIds ?? '');
    }
@endphp
<div class="topic-picker" data-mode="{{ $mode }}">
    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Temas de esta cuenta') }}</span>
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Escribe al menos 3 letras; la lista se actualiza mientras escribes.') }}</p>
    @error('topic_ids')
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror

    <ul id="topic-selected-list" class="mt-3 flex flex-wrap gap-2 list-none p-0 min-h-[2rem]">
        @foreach ($selectedTopics as $topic)
            <li class="topic-pill inline-flex items-center gap-1.5 pl-2.5 pr-1 py-1 rounded-lg text-sm bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-100 border border-indigo-200 dark:border-indigo-700"
                data-topic-id="{{ $topic->id }}">
                <span class="topic-pill-label">{{ $topic->display_name }}</span>
                <button type="button" class="topic-pill-remove shrink-0 rounded p-0.5 text-indigo-600 hover:bg-indigo-100 dark:hover:bg-indigo-800" title="{{ __('Quitar') }}" aria-label="{{ __('Quitar') }}">&times;</button>
            </li>
        @endforeach
    </ul>

    @if ($mode === 'create')
        <input type="hidden" name="topic_ids" id="topic_ids_hidden" value="{{ $initialHidden }}">
    @endif

    <div class="mt-4">
        <label for="topic-search-input" class="sr-only">{{ __('Buscar tema') }}</label>
        <input type="search" id="topic-search-input" autocomplete="off" placeholder="{{ __('Mínimo 3 caracteres…') }}"
            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <div id="topic-search-panel" class="mt-2 hidden rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 overflow-hidden">
        <div id="topic-search-loader" class="hidden items-center gap-2 px-3 py-2.5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/40">
            <span class="inline-block size-4 shrink-0 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin" aria-hidden="true"></span>
            <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Buscando…') }}</span>
        </div>
        <div id="topic-search-results" class="divide-y divide-gray-100 dark:divide-gray-700 max-h-48 overflow-y-auto"></div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function () {
    var mode = @json($mode);
    var searchUrl = @json(route('social-accounts.topics.search'));
    var csrf = $('meta[name="csrf-token"]').attr('content');
    var $list = $('#topic-selected-list');
    var $input = $('#topic-search-input');
    var $panel = $('#topic-search-panel');
    var $results = $('#topic-search-results');
    var $loader = $('#topic-search-loader');
    var $hidden = $('#topic_ids_hidden');
    var searchTimer = null;
    var searchSeq = 0;
    var debounceMs = 280;
    /** Evita reemplazar la lista mientras hay un clic/pulsación en curso sobre un resultado (el DOM vacío provoca “click-through” al botón Enviar del formulario). */
    var pointerActiveOnHit = false;

    @if ($mode === 'edit')
    var attachUrl = @json(route('social-accounts.topics.attach', $socialAccount));
    var detachBase = @json(route('social-accounts.topics.attach', $socialAccount));
    @endif

    function selectedIdsCreate() {
        var ids = [];
        $list.find('li[data-topic-id]').each(function () {
            ids.push(parseInt($(this).data('topic-id'), 10));
        });
        return ids;
    }

    function syncHiddenCreate() {
        if ($hidden.length) {
            $hidden.val(selectedIdsCreate().join(','));
        }
    }

    function appendPill(id, label) {
        id = parseInt(id, 10);
        if ($list.find('li[data-topic-id="' + id + '"]').length) {
            return;
        }
        var $li = $('<li/>', {
            'class': 'topic-pill inline-flex items-center gap-1.5 pl-2.5 pr-1 py-1 rounded-lg text-sm bg-indigo-50 dark:bg-indigo-900/30 text-indigo-900 dark:text-indigo-100 border border-indigo-200 dark:border-indigo-700',
            'data-topic-id': id
        });
        $li.append($('<span/>', { 'class': 'topic-pill-label', text: label }));
        $li.append($('<button type="button" class="topic-pill-remove shrink-0 rounded p-0.5 text-indigo-600 hover:bg-indigo-100 dark:hover:bg-indigo-800" title="{{ __('Quitar') }}">&times;</button>'));
        $list.append($li);
        syncHiddenCreate();
    }

    function hidePanelIfEmpty() {
        if (!$results.children().length && $loader.hasClass('hidden')) {
            $panel.addClass('hidden');
        }
    }

    function setLoading(on) {
        if (on) {
            $loader.removeClass('hidden').addClass('flex');
        } else {
            $loader.addClass('hidden').removeClass('flex');
        }
    }

    $list.on('click', '.topic-pill-remove', function (e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        var id = parseInt($li.data('topic-id'), 10);
        @if ($mode === 'edit')
        $.ajax({
            url: detachBase.replace(/\/?$/, '/') + id,
            type: 'POST',
            data: { _method: 'DELETE', _token: csrf }
        }).done(function () {
            $li.remove();
        });
        @else
        $li.remove();
        syncHiddenCreate();
        @endif
    });

    function renderResults(rows) {
        $results.empty();
        if (!rows || !rows.length) {
            hidePanelIfEmpty();
            return;
        }
        rows.forEach(function (row) {
            var $btn = $('<button type="button" class="topic-search-hit block w-full text-left px-3 py-2 text-sm text-indigo-700 dark:text-indigo-300 hover:bg-gray-50 dark:hover:bg-gray-700 border-0 bg-transparent cursor-pointer"></button>');
            $btn.attr('data-topic-id', row.id);
            $btn.text(row.name);
            $btn.data('id', row.id);
            $btn.data('name', row.name);
            $results.append($btn);
        });
    }

    function whenSafeToReplaceDom(fn) {
        var maxWait = 2000;
        var start = Date.now();
        function tick() {
            if (!pointerActiveOnHit) {
                fn();
                return;
            }
            if (Date.now() - start > maxWait) {
                pointerActiveOnHit = false;
                fn();
                return;
            }
            setTimeout(tick, 16);
        }
        tick();
    }

    $results.on('pointerdown', '.topic-search-hit', function () {
        pointerActiveOnHit = true;
    });
    $(document).on('pointerup pointercancel', function () {
        pointerActiveOnHit = false;
    });

    function runSearch() {
        var q = ($input.val() || '').trim();
        if (q.length < 3) {
            searchSeq++;
            setLoading(false);
            $results.empty();
            $panel.addClass('hidden');
            return;
        }
        var mySeq = ++searchSeq;
        setLoading(true);
        $panel.removeClass('hidden');
        $.getJSON(searchUrl, { q: q })
            .done(function (data) {
                if (mySeq !== searchSeq) {
                    return;
                }
                whenSafeToReplaceDom(function () {
                    if (mySeq !== searchSeq) {
                        return;
                    }
                    setLoading(false);
                    renderResults(data.topics || []);
                    if (!$results.children().length) {
                        $panel.addClass('hidden');
                    } else {
                        $panel.removeClass('hidden');
                    }
                });
            })
            .fail(function () {
                if (mySeq !== searchSeq) {
                    return;
                }
                whenSafeToReplaceDom(function () {
                    if (mySeq !== searchSeq) {
                        return;
                    }
                    setLoading(false);
                    $results.empty();
                    $panel.addClass('hidden');
                });
            });
    }

    function scheduleSearch() {
        clearTimeout(searchTimer);
        var q = ($input.val() || '').trim();
        if (q.length < 3) {
            searchSeq++;
            setLoading(false);
            $results.empty();
            $panel.addClass('hidden');
            return;
        }
        searchTimer = setTimeout(function () {
            runSearch();
        }, debounceMs);
    }

    $input.on('input', function () {
        scheduleSearch();
    });

    $input.on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            runSearch();
        }
    });

    $results.on('mousedown', '.topic-search-hit', function (e) {
        e.preventDefault();
    });

    $results.on('click', '.topic-search-hit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $link = $(this);
        var id = $link.data('id');
        var name = $link.data('name');
        if ($list.find('li[data-topic-id="' + id + '"]').length) {
            setTimeout(function () {
                $link.remove();
                hidePanelIfEmpty();
            }, 0);
            return;
        }
        function removeLinkOnly() {
            setTimeout(function () {
                $link.remove();
                if (!$results.children().length) {
                    $panel.addClass('hidden');
                }
            }, 0);
        }
        @if ($mode === 'create')
        appendPill(id, name);
        removeLinkOnly();
        @else
        $.post(attachUrl, { _token: csrf, topic_id: id })
            .done(function () {
                appendPill(id, name);
                removeLinkOnly();
            });
        @endif
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#topic-search-panel, #topic-search-input').length) {
            $panel.addClass('hidden');
        }
    });
})();
</script>
@endpush
