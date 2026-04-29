@extends('layouts.guest')

@section('title', $creator->name . ' - ' . config('app.name'))

@php
    $daysMap = [
        'mon' => __('Lun'), 'tue' => __('Mar'), 'wed' => __('Mié'), 'thu' => __('Jue'),
        'fri' => __('Vie'), 'sat' => __('Sáb'), 'sun' => __('Dom'),
    ];
    $upcomingLives = $upcomingLives ?? collect();
    $pastLives = $pastLives ?? collect();
    $followersCount = $creator->followers_count ?? 0;
    $followingCount = $creator->following_count ?? 0;
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Cabecera: avatar, nombre, nivel --}}
    <div class="bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <img src="{{ $creator->avatar_url }}" alt="" class="flex-shrink-0 w-20 h-20 rounded-full object-cover border-2 border-[#e3e3e0] dark:border-[#3E3E3A]" />
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $creator->name }}</h1>
                    @if ($creator->level)
                        <p class="text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ $creator->level->name }}</p>
                    @endif
                    {{-- Seguidores / Siguiendo --}}
                    <div class="flex gap-6 mt-4 text-sm">
                        <span class="text-[#1b1b18] dark:text-[#EDEDEC]"><strong>{{ number_format($followersCount) }}</strong> {{ __('Seguidores') }}</span>
                        <span class="text-[#1b1b18] dark:text-[#EDEDEC]"><strong>{{ number_format($followingCount) }}</strong> {{ __('Siguiendo') }}</span>
                    </div>
                </div>
            </div>

            {{-- Siguiendo: primero (lista pública del creador) --}}
            <div class="mt-8 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                    <x-feather-icon name="user-plus" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                    {{ __('Siguiendo') }}
                </h2>
                @if ($creator->followingEntries->isEmpty())
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ __('Aún no hay cuentas en esta lista pública.') }}</p>
                @else
                    <ul class="space-y-4">
                        @foreach ($creator->followingEntries as $entry)
                            <li class="flex gap-3 p-3 rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#fafafa] dark:bg-[#1a1a18]">
                                <div class="shrink-0">
                                    @if ($entry->isPlatform() && $entry->platformUser)
                                        <img src="{{ filled($entry->avatar_url) ? $entry->avatar_url : $entry->platformUser->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover border border-[#e3e3e0] dark:border-[#3E3E3A]" width="48" height="48" loading="lazy">
                                    @elseif ($entry->isExternal() && filled($entry->avatar_url))
                                        <img src="{{ $entry->avatar_url }}" alt="" class="w-12 h-12 rounded-full object-cover border border-[#e3e3e0] dark:border-[#3E3E3A]" width="48" height="48" loading="lazy" referrerpolicy="no-referrer">
                                    @else
                                        <span class="inline-flex w-12 h-12 items-center justify-center rounded-full border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#e3e3e0] dark:bg-[#3E3E3A] text-[#706f6c]" aria-hidden="true">
                                            <x-feather-icon name="user" class="w-6 h-6" />
                                        </span>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2 mb-0.5">
                                        @if ($entry->isPlatform())
                                            <span class="text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded bg-violet-100 text-violet-800 dark:bg-violet-900/40 dark:text-violet-200">{{ __('Plataforma') }}</span>
                                        @else
                                            <span class="text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ __('Externa') }}</span>
                                        @endif
                                    </div>
                                    <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $entry->displayTitle() }}</p>
                                    @if ($entry->isPlatform() && filled($entry->remote_display_name))
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Nombre en TikTok') }}: {{ $entry->remote_display_name }}</p>
                                    @endif
                                    @if ($entry->latestFollowerSnapshot && $entry->latestFollowerSnapshot->follower_count !== null)
                                        <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ __('Seguidores (última lectura)') }}: {{ number_format($entry->latestFollowerSnapshot->follower_count) }}</p>
                                    @endif
                                    <a href="{{ $entry->url }}" target="_blank" rel="noopener noreferrer" title="{{ $entry->url }}" class="text-sm text-[#F53003] dark:text-[#FF4433] hover:underline break-all mt-1 inline-block">
                                        @if ($entry->isExternal() && filled($entry->username))
                                            {{ '@'.ltrim((string) $entry->username, '@') }}
                                        @else
                                            {{ $entry->url }}
                                        @endif
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($creator->personalLinks->isNotEmpty())
                <div class="mt-8 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                        <x-feather-icon name="link" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                        {{ __('Enlaces') }}
                    </h2>
                    <ul class="space-y-2">
                        @foreach ($creator->personalLinks as $pLink)
                            <li>
                                <a href="{{ $pLink->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-[#F53003] dark:text-[#FF4433] hover:underline font-medium">
                                    <x-feather-icon name="external-link" class="w-4 h-4 shrink-0" />
                                    {{ $pLink->label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Redes sociales (siempre la sección) — iconos SVG por red --}}
            <div class="mt-8 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                    <x-feather-icon name="share-2" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                    {{ __('Redes sociales') }}
                </h2>
                @if ($creator->socialAccounts->isNotEmpty())
                    <ul class="grid gap-3 [grid-template-columns:repeat(auto-fill,minmax(9.5rem,12.5rem))] sm:justify-start justify-center max-w-5xl mx-auto sm:mx-0">
                        @foreach ($creator->socialAccounts->sortBy(fn ($a) => strtolower($a->socialNetwork->name ?? '')) as $account)
                            @if ($account->isUsableOnPublicProfile())
                                @php
                                    $netSlug = $account->socialNetwork->slug ?? '';
                                    $label = $account->display_name ?: ($account->socialNetwork->name ?? __('Red'));
                                    $usernameLine = in_array($netSlug, ['tiktok', 'instagram', 'threads', 'twitter', 'x'], true)
                                        ? '@' . ltrim($account->username, '@')
                                        : $account->username;
                                    $touchVerifyUrl = (! $account->is_verified && $account->url)
                                        ? \Illuminate\Support\Facades\URL::signedRoute(
                                            'public.social-account.touch-verify',
                                            ['socialAccount' => $account],
                                            now()->addDays(14)
                                        )
                                        : '';
                                @endphp
                                <li class="min-w-0 w-full max-w-[200px] mx-auto sm:mx-0">
                                    @if ($account->url)
                                        <a href="{{ $account->url }}" target="_blank" rel="noopener noreferrer" @if ($touchVerifyUrl !== '') data-touch-verify-url="{{ $touchVerifyUrl }}" data-social-account-id="{{ $account->id }}" @endif class="group flex aspect-square w-full flex-col items-center justify-between rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#fafafa] dark:bg-[#1a1a18] p-3 text-center shadow-sm transition-colors hover:border-[#F53003]/45 dark:hover:border-[#FF4433]/45 hover:shadow-md">
                                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#e3e3e0] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] group-hover:text-[#F53003] dark:group-hover:text-[#FF4433]">
                                                <x-social-network-icon :slug="$netSlug" class="h-7 w-7" />
                                            </span>
                                            <span class="min-h-0 w-full flex-1 flex flex-col items-center justify-center gap-0.5 px-0.5">
                                                <span class="line-clamp-2 text-xs font-semibold leading-tight text-[#1b1b18] dark:text-[#EDEDEC]">{{ $label }}</span>
                                                <span class="line-clamp-2 w-full text-[11px] leading-tight text-[#706f6c] dark:text-[#A1A09A] break-all">{{ $usernameLine }}</span>
                                                @if ($account->is_verified)
                                                    <span class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-green-600 dark:text-green-400">{{ __('Verificado') }}</span>
                                                @endif
                                            </span>
                                            <x-feather-icon name="external-link" class="h-4 w-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A] group-hover:text-[#F53003] dark:group-hover:text-[#FF4433]" />
                                        </a>
                                    @else
                                        <div class="flex aspect-square w-full flex-col items-center justify-between rounded-2xl border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#fafafa]/80 dark:bg-[#1a1a18]/80 p-3 text-center opacity-85">
                                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-[#e3e3e0] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]">
                                                <x-social-network-icon :slug="$netSlug" class="h-7 w-7" />
                                            </span>
                                            <span class="min-h-0 w-full flex-1 flex flex-col items-center justify-center gap-0.5 px-0.5">
                                                <span class="line-clamp-2 text-xs font-semibold leading-tight text-[#1b1b18] dark:text-[#EDEDEC]">{{ $label }}</span>
                                                <span class="line-clamp-2 w-full text-[11px] leading-tight text-[#706f6c] dark:text-[#A1A09A] break-all">{{ $usernameLine }}</span>
                                            </span>
                                            <span class="h-4 w-4 shrink-0" aria-hidden="true"></span>
                                        </div>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ __('Sin redes enlazadas.') }}</p>
                @endif
            </div>

            {{-- Temas (siempre la sección) --}}
            <div class="mt-8 pt-6 border-t border-[#e3e3e0] dark:border-[#3E3E3A]">
                <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                    <x-feather-icon name="tag" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                    {{ __('Temas') }}
                </h2>
                @if ($creator->topics->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($creator->topics as $topic)
                            <a href="{{ route('explore', ['topic' => $topic->slug]) }}" class="inline-flex px-3 py-1.5 rounded-lg text-sm font-medium bg-[#e3e3e0] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] hover:bg-[#d0d0ce] dark:hover:bg-gray-600 transition-colors">
                                {{ $topic->display_name }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ __('Sin temas asignados.') }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Horarios (siempre la sección) --}}
    <div class="bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                <x-feather-icon name="clock" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                {{ __('Horarios') }}
            </h2>
            @if ($creator->schedules->isNotEmpty())
                <ul class="space-y-4">
                    @foreach ($creator->schedules as $schedule)
                        <li class="flex flex-wrap items-center gap-2 py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A] last:border-0 last:pb-0">
                            <span class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                                {{ collect($schedule->days ?? [])->map(fn ($d) => $daysMap[$d] ?? $d)->join(', ') }}
                            </span>
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">·</span>
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">{{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ __('Sin horarios configurados.') }}</p>
            @endif
        </div>
    </div>

    {{-- Encuestas (siempre la sección) --}}
    <div class="bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                <x-feather-icon name="bar-chart-2" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                {{ __('Encuestas') }}
            </h2>
            @if (isset($creator->polls) && $creator->polls->isNotEmpty())
                <ul class="space-y-5">
                    @foreach ($creator->polls as $poll)
                        @php
                            $pollOpen = $poll->isOpen();
                            $totalVotes = $poll->pollOptions->sum('votes_count');
                        @endphp
                        <li class="rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] p-4 poll-vote-widget"
                            data-post-url="{{ route('public.polls.vote', $poll) }}"
                            data-poll-id="{{ $poll->id }}">
                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $poll->question }}</p>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">
                                {{ $poll->is_active ? __('Activa') : __('Cerrada') }}
                                @if ($poll->expires_at)
                                    · {{ __('Expira') }} {{ \Carbon\Carbon::parse($poll->expires_at)->translatedFormat('d/m/Y') }}
                                @endif
                                · <span class="poll-total-votes tabular-nums">{{ $totalVotes }}</span> {{ __('votos') }}
                            </p>
                            @if ($pollOpen && $poll->pollOptions->isNotEmpty())
                                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-2">{{ __('Pulsa una opción para votar. Puedes cambiar tu voto.') }}</p>
                                <div class="flex flex-wrap gap-2 mt-3">
                                    @foreach ($poll->pollOptions as $opt)
                                        <button type="button"
                                            class="poll-vote-option inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] hover:border-[#F53003] dark:hover:border-[#FF4433] transition-colors text-[#1b1b18] dark:text-[#EDEDEC]"
                                            data-option-id="{{ $opt->id }}">
                                            <span>{{ $opt->text }}</span>
                                            <span class="text-[#706f6c] dark:text-[#A1A09A] poll-option-count tabular-nums" data-option-id="{{ $opt->id }}">{{ $opt->votes_count }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif (!$pollOpen && $poll->pollOptions->isNotEmpty())
                                <div class="flex flex-wrap gap-2 mt-3 text-sm">
                                    @foreach ($poll->pollOptions as $opt)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-[#e3e3e0] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]">
                                            {{ $opt->text }}
                                            <strong class="poll-option-count tabular-nums" data-option-id="{{ $opt->id }}">{{ $opt->votes_count }}</strong>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ __('Sin encuestas publicadas.') }}</p>
            @endif
        </div>
    </div>

    {{-- Directos (siempre la sección) --}}
    <div class="bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                <x-feather-icon name="play-circle" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                {{ __('Directos') }}
            </h2>
            @if ($upcomingLives->isNotEmpty())
                <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Próximos directos') }}</p>
                <ul class="space-y-3 mb-6">
                    @foreach ($upcomingLives as $live)
                        <li class="py-2">
                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $live->title }}</p>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $live->scheduled_at?->translatedFormat('d/m/Y H:i') }}</p>
                            @if ($live->description)
                                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mt-1">{{ Str::limit($live->description, 120) }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
            @if ($pastLives->isNotEmpty())
                <p class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A] mb-2">{{ __('Historial de directos') }}</p>
                <ul class="space-y-3">
                    @foreach ($pastLives as $live)
                        <li class="py-2 border-b border-[#e3e3e0] dark:border-[#3E3E3A] last:border-0">
                            <p class="font-medium text-[#1b1b18] dark:text-[#EDEDEC]">{{ $live->title }}</p>
                            <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ $live->scheduled_at?->translatedFormat('d/m/Y H:i') }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
            @if ($upcomingLives->isEmpty() && $pastLives->isEmpty())
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">{{ __('Sin directos anunciados.') }}</p>
            @endif
        </div>
    </div>

    {{-- Mensajes: solo indicar que es función para usuarios registrados --}}
    <div class="bg-white dark:bg-[#161615] rounded-xl border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC] mb-4 flex items-center gap-2">
                <x-feather-icon name="message-circle" class="w-5 h-5 text-[#706f6c] dark:text-[#A1A09A]" />
                {{ __('Mensajes') }}
            </h2>
            <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm">
                {{ __('Para enviar mensajes a este creador, inicia sesión o regístrate.') }}
            </p>
            @guest
                <a href="{{ route('login') }}" class="inline-block mt-3 px-4 py-2 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC] text-sm font-medium hover:bg-[#e3e3e0] dark:hover:bg-[#3E3E3A]">{{ __('Iniciar sesión para contactar') }}</a>
            @else
                <a href="{{ url('/dashboard') }}" class="inline-block mt-3 px-4 py-2 rounded-lg bg-[#F53003] dark:bg-[#FF4433] text-white text-sm font-medium hover:opacity-90">{{ __('Enviar mensaje') }}</a>
            @endguest
        </div>
    </div>

    <p class="mt-6">
        <a href="{{ route('explore') }}" class="text-sm font-medium text-[#F53003] dark:text-[#FF4433] hover:underline">{{ __('Volver a creadores') }}</a>
    </p>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
<script>
    window.pollUserAuthenticated = @json(auth()->check());
    (function ($) {
        function guestToken() {
            if (window.pollUserAuthenticated) {
                return null;
            }
            var k = 'poll_guest_token';
            var t = localStorage.getItem(k);
            if (!t && window.crypto && crypto.randomUUID) {
                t = crypto.randomUUID();
                localStorage.setItem(k, t);
            }
            return t;
        }
        $(document).on('click', '.poll-vote-option', function () {
            var $btn = $(this);
            var $widget = $btn.closest('.poll-vote-widget');
            var url = $widget.data('post-url');
            var payload = { poll_option_id: $btn.data('option-id') };
            if (!window.pollUserAuthenticated) {
                var gt = guestToken();
                if (!gt) {
                    alert(@json(__('Tu navegador no permite guardar un voto anónimo estable. Prueba otro navegador o inicia sesión.')));
                    return;
                }
                payload.guest_token = gt;
            }
            $.ajax({
                url: url,
                method: 'POST',
                data: payload,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
                .done(function (res) {
                    if (!res.options) {
                        return;
                    }
                    var total = 0;
                    $.each(res.options, function (id, n) {
                        total += n;
                        $widget.find('.poll-option-count[data-option-id="' + id + '"]').text(n);
                    });
                    $widget.find('.poll-total-votes').text(total);
                })
                .fail(function (xhr) {
                    var msg =
                        xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : @json(__('No se pudo registrar el voto.'));
                    alert(msg);
                });
        });
    })(jQuery);

    (function () {
        document.querySelectorAll('a[data-touch-verify-url]').forEach(function (el) {
            el.addEventListener('click', function () {
                var url = el.getAttribute('data-touch-verify-url');
                if (!url) return;
                var meta = document.querySelector('meta[name="csrf-token"]');
                var csrf = meta ? meta.getAttribute('content') : '';
                if (!csrf) return;
                try {
                    fetch(url, {
                        method: 'POST',
                        keepalive: true,
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            Accept: 'application/json',
                        },
                    }).catch(function () {});
                } catch (e) {}
            });
        });
    })();
</script>
@endpush
