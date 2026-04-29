@extends('layouts.creator.profile-app')

@section('title', __('Perfil') . ' - ' . config('app.name'))

@section('header_title', __('Perfil'))

@section('content')
@php
    $user = auth()->user();
    $user->loadCount(['socialAccounts', 'followers', 'following', 'schedules', 'polls', 'liveAnnouncements', 'personalLinks']);
@endphp

<div class="profile-area">
    {{-- Bloque principal (estilo Soziety / profile.html) --}}
    <div class="profile rounded-2xl bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-4">
        <div class="main-profile flex flex-wrap items-start justify-between gap-4 p-5">
            <div class="left-content min-w-0">
                <span class="text-sm font-medium text-[#2196f3] dark:text-indigo-400">{{ '@' . Str::slug($user->name, '') }}</span>
                <h5 class="font-template-title font-semibold text-lg text-gray-900 dark:text-white mt-1 mb-0">{{ $user->name }}</h5>
                @if ($user->level)
                    <h6 class="text-sm font-normal text-[#2196f3] dark:text-indigo-400 mt-0.5 mb-0">{{ $user->level->name }}</h6>
                @else
                    <h6 class="text-sm font-normal text-gray-500 dark:text-gray-400 mt-0.5 mb-0">{{ $user->role === 'creator' ? __('Creador') : __('Usuario') }}</h6>
                @endif
            </div>
            <div class="right-content shrink-0">
                <div class="upload-box relative">
                    <img src="{{ $user->avatar_url }}" alt="" class="w-24 h-24 rounded-2xl object-cover border-2 border-gray-200 dark:border-gray-600 shadow-inner">
                    <a href="{{ route('profile.edit') }}" class="upload-btn absolute -bottom-1 -right-1 w-9 h-9 rounded-xl bg-[#2196f3] dark:bg-indigo-600 text-white flex items-center justify-center shadow-md hover:opacity-90" title="{{ __('Editar perfil') }}">
                        <x-feather-icon name="edit-2" class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </div>
        @if ($user->email)
        <div class="info px-5 pb-5 border-t border-gray-100 dark:border-gray-700/80">
            <h6 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('Sobre mí') }}</h6>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-0">{{ $user->email }}</p>
        </div>
        @endif
    </div>

    <div class="contant-section">
        <div class="social-bar rounded-2xl bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden mb-4">
            <ul class="grid grid-cols-3 divide-x divide-gray-200 dark:divide-gray-700 list-none m-0 p-0">
                <li class="active">
                    <a href="{{ route('social-accounts.index') }}" class="block py-4 text-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors no-underline">
                        <h4 class="font-template-title font-semibold text-lg text-gray-900 dark:text-white mb-0">{{ $user->social_accounts_count }}</h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Redes') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/profile#following') }}" class="block py-4 text-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors no-underline">
                        <h4 class="font-template-title font-semibold text-lg text-gray-900 dark:text-white mb-0">{{ $user->following_count }}</h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Siguiendo') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('/profile#followers') }}" class="block py-4 text-center hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors no-underline">
                        <h4 class="font-template-title font-semibold text-lg text-gray-900 dark:text-white mb-0">{{ $user->followers_count }}</h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('Seguidores') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="title-bar flex items-center justify-between gap-3 my-3">
            <h6 class="font-template-title font-semibold text-gray-900 dark:text-white mb-0">{{ __('Mis contenidos') }}</h6>
            <div class="dz-tab style-2 flex rounded-xl border border-gray-200 dark:border-gray-700 p-0.5 bg-white/80 dark:bg-gray-800/80" role="tablist">
                <button type="button" class="profile-tab-btn nav-link px-2 py-1.5 rounded-lg text-[#2196f3] dark:text-indigo-400 bg-blue-50 dark:bg-indigo-900/40" data-profile-tab="grid" aria-selected="true" title="{{ __('Vista cuadrícula') }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M10 3H3V10H10V3Z" stroke="currentColor" stroke-width="2" stroke-opacity="0.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 3H14V10H21V3Z" stroke="currentColor" stroke-width="2" stroke-opacity="0.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 14H14V21H21V14Z" stroke="currentColor" stroke-width="2" stroke-opacity="0.7" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 14H3V21H10V14Z" stroke="currentColor" stroke-width="2" stroke-opacity="0.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" class="profile-tab-btn nav-link px-2 py-1.5 rounded-lg text-gray-500 dark:text-gray-400" data-profile-tab="list" aria-selected="false" title="{{ __('Vista lista') }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M8 6H21" stroke="currentColor" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 12H21" stroke="currentColor" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 18H21" stroke="currentColor" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 6H3.01" stroke="currentColor" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 12H3.01" stroke="currentColor" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 18H3.01" stroke="currentColor" stroke-opacity="0.6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="tab-content" id="profile-tab-panels">
            <div class="profile-tab-panel" data-panel="grid" role="tabpanel">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @if ($user->role === 'creator')
                    <a href="{{ route('personal-links.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/80 p-4 hover:shadow-md hover:border-[#2196f3]/40 dark:hover:border-indigo-500/40 transition-all no-underline">
                        <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 mb-2">
                            <x-feather-icon name="link" class="w-5 h-5" />
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-0">{{ __('Enlaces') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0">{{ $user->personal_links_count ?? 0 }}</p>
                    </a>
                    @endif
                    <a href="{{ route('social-accounts.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/80 p-4 hover:shadow-md hover:border-[#2196f3]/40 dark:hover:border-indigo-500/40 transition-all no-underline">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-indigo-900/40 flex items-center justify-center text-[#2196f3] dark:text-indigo-400 mb-2">
                            <x-feather-icon name="link-2" class="w-5 h-5" />
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-0">{{ __('Redes') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0">{{ $user->social_accounts_count }}</p>
                    </a>
                    <a href="{{ route('schedules.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/80 p-4 hover:shadow-md hover:border-[#2196f3]/40 dark:hover:border-indigo-500/40 transition-all no-underline">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-2">
                            <x-feather-icon name="clock" class="w-5 h-5" />
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-0">{{ __('Horarios') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0">{{ $user->schedules_count ?? 0 }}</p>
                    </a>
                    <a href="{{ route('polls.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/80 p-4 hover:shadow-md hover:border-[#2196f3]/40 dark:hover:border-indigo-500/40 transition-all no-underline">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-2">
                            <x-feather-icon name="bar-chart-2" class="w-5 h-5" />
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-0">{{ __('Encuestas') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0">{{ $user->polls_count ?? 0 }}</p>
                    </a>
                    <a href="{{ route('live-announcements.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/80 p-4 hover:shadow-md hover:border-[#2196f3]/40 dark:hover:border-indigo-500/40 transition-all no-underline">
                        <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center text-rose-600 dark:text-rose-400 mb-2">
                            <x-feather-icon name="play-circle" class="w-5 h-5" />
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-0">{{ __('Lives') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0">{{ $user->live_announcements_count ?? 0 }}</p>
                    </a>
                    <a href="{{ route('user-topics.index') }}" class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/80 p-4 hover:shadow-md hover:border-[#2196f3]/40 dark:hover:border-indigo-500/40 transition-all no-underline sm:col-span-1 col-span-2">
                        <div class="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center text-violet-600 dark:text-violet-400 mb-2">
                            <x-feather-icon name="tag" class="w-5 h-5" />
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-0">{{ __('Mis temas') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-0">{{ __('Afiliarte o quitar temas') }}</p>
                    </a>
                </div>
            </div>
            <div class="profile-tab-panel hidden" data-panel="list" role="tabpanel">
                <ul class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white/90 dark:bg-gray-800/90 divide-y divide-gray-100 dark:divide-gray-700 list-none m-0 p-0 overflow-hidden">
                    @if ($user->role === 'creator')
                    <li><a href="{{ route('personal-links.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 no-underline text-sm font-medium"><x-feather-icon name="link" class="w-5 h-5 text-slate-600 shrink-0" />{{ __('Enlaces personales') }} <span class="ml-auto text-xs text-gray-500">{{ $user->personal_links_count ?? 0 }}</span></a></li>
                    @endif
                    <li><a href="{{ route('social-accounts.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 no-underline text-sm font-medium"><x-feather-icon name="link-2" class="w-5 h-5 text-[#2196f3] shrink-0" />{{ __('Redes') }} <span class="ml-auto text-xs text-gray-500">{{ $user->social_accounts_count }}</span></a></li>
                    <li><a href="{{ route('schedules.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 no-underline text-sm font-medium"><x-feather-icon name="clock" class="w-5 h-5 text-emerald-600 shrink-0" />{{ __('Horarios') }} <span class="ml-auto text-xs text-gray-500">{{ $user->schedules_count ?? 0 }}</span></a></li>
                    <li><a href="{{ route('polls.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 no-underline text-sm font-medium"><x-feather-icon name="bar-chart-2" class="w-5 h-5 text-amber-600 shrink-0" />{{ __('Encuestas') }} <span class="ml-auto text-xs text-gray-500">{{ $user->polls_count ?? 0 }}</span></a></li>
                    <li><a href="{{ route('live-announcements.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 no-underline text-sm font-medium"><x-feather-icon name="play-circle" class="w-5 h-5 text-rose-600 shrink-0" />{{ __('Lives') }} <span class="ml-auto text-xs text-gray-500">{{ $user->live_announcements_count ?? 0 }}</span></a></li>
                    <li><a href="{{ route('user-topics.index') }}" class="flex items-center gap-3 px-4 py-3.5 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 no-underline text-sm font-medium"><x-feather-icon name="tag" class="w-5 h-5 text-violet-600 shrink-0" />{{ __('Mis temas') }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var buttons = document.querySelectorAll('.profile-tab-btn');
    var panels = document.querySelectorAll('.profile-tab-panel');
    if (!buttons.length || !panels.length) return;
    function activate(name) {
        buttons.forEach(function(btn) {
            var on = btn.getAttribute('data-profile-tab') === name;
            btn.setAttribute('aria-selected', on ? 'true' : 'false');
            btn.classList.toggle('text-[#2196f3]', on);
            btn.classList.toggle('dark:text-indigo-400', on);
            btn.classList.toggle('bg-blue-50', on);
            btn.classList.toggle('dark:bg-indigo-900/40', on);
            btn.classList.toggle('text-gray-500', !on);
            btn.classList.toggle('dark:text-gray-400', !on);
        });
        panels.forEach(function(p) {
            var show = p.getAttribute('data-panel') === name;
            p.classList.toggle('hidden', !show);
        });
    }
    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            activate(btn.getAttribute('data-profile-tab'));
        });
    });
})();
</script>
@endpush
@endsection
