<header class="fixed top-0 left-0 right-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-200 dark:border-gray-700 shadow-sm pt-[env(safe-area-inset-top)]">
    <div class="max-w-2xl mx-auto px-4 h-14 flex items-center justify-between gap-2">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <a href="javascript:history.length > 1 ? history.back() : '{{ url('/') }}'" class="p-2 -ml-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 shrink-0" title="{{ __('Volver') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="font-template-title font-semibold text-base text-gray-900 dark:text-white truncate mb-0">{{ $headerTitle ?? __('Perfil') }}</h1>
        </div>
        <div class="mid-content hidden sm:block w-8 shrink-0" aria-hidden="true"></div>
        <x-appearance-switch compact class="shrink-0" />
        <div class="relative shrink-0" data-profile-gear>
            <button type="button" class="bell-icon p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" data-profile-gear-toggle aria-expanded="false" aria-haspopup="true" title="{{ __('Menú') }}">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19.43 12.98c.04-.32.07-.64.07-.98s-.03-.66-.07-.98l2.11-1.65c.19-.15.24-.42.12-.64l-2-3.46a.5.5 0 0 0-.44-.25c-.06 0-.12.01-.17.03l-2.49 1c-.52-.4-1.08-.73-1.69-.98l-.38-2.65C14.46 2.18 14.25 2 14 2h-4c-.25 0-.46.18-.49.42l-.38 2.65c-.61.25-1.17.59-1.69.98l-2.49-1c-.06-.02-.12-.03-.18-.03-.17 0-.34.09-.43.25l-2 3.46c-.13.22-.07.49.12.64l2.11 1.65a7.93 7.93 0 0 0-.07.98 7.93 7.93 0 0 0 .07.98l-2.11 1.65c-.19.15-.24.42-.12.64l2 3.46a.5.5 0 0 0 .44.25c.06 0 .12-.01.17-.03l2.49-1c.52.4 1.08.73 1.69.98l.38 2.65c.03.24.24.42.49.42h4c.25 0 .46-.18.49-.42l.38-2.65c.61-.25 1.17-.59 1.69-.98l2.49 1c.06.02.12.03.18.03.17 0 .34-.09.43-.25l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.65zm-1.98-1.71c.04.31.05.52.05.73s-.02.43-.05.73l-.14 1.13.89.7 1.08.84-.7 1.21-1.27-.51-1.04-.42-.9.68c-.43.32-.84.56-1.25.73l-1.06.43-.16 1.13-.2 1.35h-1.4l-.19-1.35-.16-1.13-1.06-.43c-.43-.18-.83-.41-1.23-.71l-.91-.7-1.06.43-1.27.51-.7-1.21 1.08-.84.89-.7-.14-1.13L6.5 12c0-.2.02-.43.05-.73l.14-1.13-.89-.7-1.08-.84.7-1.21 1.27.51 1.04.42.9-.68c.43-.32.84-.56 1.25-.73l1.06-.43.16-1.13.2-1.35h1.39l.19 1.35.16 1.13 1.06.43c.43.18.83.41 1.23.71l.91.7 1.06-.43 1.27-.51.7 1.21-1.07.85-.89.7.14 1.13zM12 8a4 4 0 1 0 0 8 4 4 0 1 0 0-8zm0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
            </button>
            <div class="hidden absolute right-0 top-full mt-1 w-72 max-w-[85vw] rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-xl py-2 z-50" data-profile-gear-panel role="menu">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Hola') }}</p>
                    <p class="font-template-title font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                </div>
                <nav class="max-h-[min(70vh,24rem)] overflow-y-auto scrollbar-hide py-1">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="home" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Inicio') }}</a>
                    <a href="{{ route('explore') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="search" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Explorar') }}</a>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="grid" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Mi panel') }}</a>
                    <a href="{{ route('following.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="user-plus" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Siguiendo') }}</a>
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="user" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Perfil') }}</a>
                    <a href="{{ route('configuration.show') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="settings" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Configuración') }}</a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="edit-2" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Editar perfil') }}</a>
                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('profile-app-logout-form').submit();" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800" role="menuitem"><x-feather-icon name="log-out" class="w-5 h-5 text-gray-500 shrink-0" />{{ __('Cerrar sesión') }}</a>
                </nav>
                <form id="profile-app-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            </div>
        </div>
    </div>
</header>

@push('scripts')
<script>
(function() {
    var root = document.querySelector('[data-profile-gear]');
    if (!root) return;
    var btn = root.querySelector('[data-profile-gear-toggle]');
    var panel = root.querySelector('[data-profile-gear-panel]');
    if (!btn || !panel) return;
    function close() {
        panel.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
    }
    function toggle() {
        panel.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', panel.classList.contains('hidden') ? 'false' : 'true');
    }
    btn.addEventListener('click', function(e) { e.stopPropagation(); toggle(); });
    document.addEventListener('click', function() { close(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') close(); });
    panel.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
@endpush
