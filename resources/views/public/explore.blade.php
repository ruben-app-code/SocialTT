@extends('layouts.guest')

@section('title', __('Explorar creadores') . ' - ' . config('app.name'))

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-4">{{ __('Creadores') }}</h1>

    @if(($currentTopic ?? null) && !($selectedTopic ?? null))
        <div class="mb-4 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/30 px-4 py-3 text-sm text-amber-900 dark:text-amber-100">
            {{ __('El tema de la URL no existe. Busca un tema válido abajo o visita la página de temas.') }}
            <a href="{{ route('public.topics') }}" class="ml-1 font-medium text-[#F53003] dark:text-[#FF4433] hover:underline">{{ __('Ver temas') }}</a>
        </div>
    @endif

    @if($selectedTopic ?? null)
        <div class="mb-5 rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">{{ __('Tema activo') }}</p>
            @if($selectedTopic->parent)
                <p class="mt-1 text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $selectedTopic->parent->name }}</p>
                <p class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $selectedTopic->name }}</p>
                <p class="mt-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Subtema') }}</p>
            @else
                <p class="mt-1 text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $selectedTopic->name }}</p>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Tema general — se incluyen creadores de este tema y sus subtemas.') }}</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1">
                <a href="{{ route('explore', array_filter(['q' => $searchQuery])) }}" class="text-sm font-medium text-[#F53003] dark:text-[#FF4433] hover:underline">{{ __('Quitar filtro de tema') }}</a>
                <a href="{{ route('public.topics') }}" class="text-sm text-[#706f6c] dark:text-[#A1A09A] hover:text-[#F53003] dark:hover:text-[#FF4433] hover:underline">{{ __('Ver todos los temas') }}</a>
            </div>
        </div>
    @endif

    <div class="mb-6 max-w-xl">
        <label for="explore-topic-search-input" class="block text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ __('Filtrar por tema') }}</label>
        <p class="mt-1 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Escribe al menos 3 letras; la lista se actualiza mientras escribes. Al elegir un tema se acota el listado de creadores.') }}</p>
        <input type="search" id="explore-topic-search-input" autocomplete="off" placeholder="{{ __('Mínimo 3 caracteres…') }}"
            class="mt-2 block w-full rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] px-4 py-2 text-sm text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] dark:placeholder-[#A1A09A] focus:ring-2 focus:ring-[#F53003] dark:focus:ring-[#FF4433] focus:border-transparent">
        <div id="explore-topic-search-panel" class="mt-2 hidden rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] overflow-hidden shadow-sm max-h-48 overflow-y-auto">
            <div id="explore-topic-search-loader" class="hidden items-center gap-2 px-3 py-2.5 border-b border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#FDFDFC] dark:bg-[#0a0a0a]">
                <span class="inline-block size-4 shrink-0 border-2 border-[#F53003] dark:border-[#FF4433] border-t-transparent rounded-full animate-spin" aria-hidden="true"></span>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Buscando…') }}</span>
            </div>
            <div id="explore-topic-search-results" class="divide-y divide-[#e3e3e0] dark:divide-[#3E3E3A]"></div>
        </div>
    </div>

    <form action="{{ route('explore') }}" method="GET" id="explore-creators-form" class="flex flex-col sm:flex-row gap-3 mb-8">
        <div class="flex-1 flex gap-2">
            <input type="search" name="q" id="explore-creator-q" value="{{ old('q', $searchQuery ?? '') }}" placeholder="{{ __('Buscar por nombre o usuario...') }}"
                class="flex-1 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] px-4 py-2 text-[#1b1b18] dark:text-[#EDEDEC] placeholder-[#706f6c] dark:placeholder-[#A1A09A] focus:ring-2 focus:ring-[#F53003] dark:focus:ring-[#FF4433] focus:border-transparent">
            @if($selectedTopic ?? null)
                <input type="hidden" name="topic" value="{{ $selectedTopic->slug }}">
            @endif
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#F53003] dark:bg-[#FF4433] text-white font-medium hover:opacity-90">
                <x-feather-icon name="search" class="w-5 h-5" />{{ __('Buscar') }}
            </button>
        </div>
    </form>
</div>

@if ($creators->isEmpty())
    <p class="text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ __('No hay creadores con esos filtros.') }}</p>
    <a href="{{ route('explore') }}" class="inline-block px-5 py-2 rounded-sm text-sm font-medium border border-[#19140035] dark:border-[#3E3E3A]">{{ __('Quitar filtros') }}</a>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($creators as $creator)
            <div class="flex flex-col bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden hover:shadow-md transition-shadow">
                <a href="{{ $creator->creator_profile_url }}" class="block p-6">
                    <div class="flex items-center gap-4">
                        <img src="{{ $creator->avatar_url }}" alt="" class="flex-shrink-0 w-14 h-14 rounded-full object-cover border-2 border-[#e3e3e0] dark:border-[#3E3E3A]" />
                        <div class="min-w-0 flex-1">
                            <h2 class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] truncate">{{ $creator->name }}</h2>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $creator->social_accounts_count }} {{ __('redes') }}</p>
                        </div>
                    </div>
                </a>
                @if ($creator->topics->isNotEmpty())
                    <div class="px-6 pb-4 pt-0 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                        <p class="pt-3 text-[10px] font-semibold uppercase tracking-wide text-[#706f6c] dark:text-[#A1A09A]">{{ __('Temas') }}</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($creator->topics as $topic)
                                <a href="{{ route('explore', ['topic' => $topic->slug]) }}" class="inline-flex max-w-full items-center rounded-md border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#FDFDFC] dark:bg-[#0a0a0a] px-2 py-0.5 text-xs text-[#1b1b18] dark:text-[#EDEDEC] hover:border-[#F53003]/50 dark:hover:border-[#FF4433]/50 hover:text-[#F53003] dark:hover:text-[#FF4433] transition-colors truncate" title="{{ $topic->display_name }}">
                                    {{ $topic->display_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $creators->links() }}
    </div>
@endif

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
(function () {
    var searchUrl = @json(route('explore.topics.search'));
    var exploreBase = @json(route('explore'));
    var $input = $('#explore-topic-search-input');
    var $panel = $('#explore-topic-search-panel');
    var $results = $('#explore-topic-search-results');
    var $loader = $('#explore-topic-search-loader');
    var $creatorQ = $('#explore-creator-q');
    var searchTimer = null;
    var searchSeq = 0;
    var debounceMs = 280;
    var pointerActiveOnHit = false;

    function setLoading(on) {
        if (on) {
            $loader.removeClass('hidden').addClass('flex');
        } else {
            $loader.addClass('hidden').removeClass('flex');
        }
    }

    function hidePanelIfEmpty() {
        if (!$results.children().length && $loader.hasClass('hidden')) {
            $panel.addClass('hidden');
        }
    }

    function goToTopic(slug) {
        var params = new URLSearchParams();
        params.set('topic', slug);
        var q = ($creatorQ.val() || '').trim();
        if (q) {
            params.set('q', q);
        }
        window.location.href = exploreBase + (params.toString() ? ('?' + params.toString()) : '');
    }

    function renderResults(rows) {
        $results.empty();
        if (!rows || !rows.length) {
            hidePanelIfEmpty();
            return;
        }
        rows.forEach(function (row) {
            var $btn = $('<button type="button" class="explore-topic-hit block w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800 border-0 bg-transparent cursor-pointer"></button>');
            $btn.attr('data-slug', row.slug);
            $btn.text(row.name);
            $btn.data('slug', row.slug);
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

    $results.on('pointerdown', '.explore-topic-hit', function () {
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
        searchTimer = setTimeout(runSearch, debounceMs);
    }

    $input.on('input', scheduleSearch);

    $input.on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimer);
            runSearch();
        }
    });

    $results.on('mousedown', '.explore-topic-hit', function (e) {
        e.preventDefault();
    });

    $results.on('click', '.explore-topic-hit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var slug = $(this).data('slug');
        if (slug) {
            goToTopic(slug);
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#explore-topic-search-panel, #explore-topic-search-input').length) {
            $panel.addClass('hidden');
        }
    });
})();
</script>
@endpush
@endsection
