{{-- Barra inferior tipo Soziety / app (móvil; en escritorio opcional) --}}
<nav class="menubar-area fixed bottom-0 left-0 right-0 z-30 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 pb-[env(safe-area-inset-bottom)] md:hidden" aria-label="{{ __('Navegación principal') }}">
    <div class="toolbar-inner max-w-2xl mx-auto flex items-center justify-around h-16 px-1">
        <a href="{{ url('/') }}" class="nav-link flex flex-col items-center justify-center gap-0.5 min-w-0 flex-1 py-2 text-gray-500 dark:text-gray-400 hover:text-[#2196f3] dark:hover:text-indigo-400 {{ request()->routeIs('home') ? 'text-[#2196f3] dark:text-indigo-400' : '' }}" title="{{ __('Inicio') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" class="opacity-80" aria-hidden="true"><path d="M21.44 11.035a.75.75 0 0 1-.69.465H18.5V19a2.25 2.25 0 0 1-2.25 2.25h-3a.75.75 0 0 1-.75-.75V16a.75.75 0 0 0-.75-.75h-1.5a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 1-.75.75h-3A2.25 2.25 0 0 1 3.5 19v-7.5H1.25a.75.75 0 0 1-.69-.465.75.75 0 0 1 .158-.818l9.75-9.75A.75.75 0 0 1 11 .246a.75.75 0 0 1 .533.222l9.75 9.75a.75.75 0 0 1 .158.818z"/></svg>
            <span class="text-[10px] font-medium truncate w-full text-center">{{ __('Inicio') }}</span>
        </a>
        <a href="{{ route('explore') }}" class="nav-link flex flex-col items-center justify-center gap-0.5 min-w-0 flex-1 py-2 text-gray-500 dark:text-gray-400 hover:text-[#2196f3] dark:hover:text-indigo-400 {{ request()->routeIs('explore') ? 'text-[#2196f3] dark:text-indigo-400' : '' }}" title="{{ __('Explorar') }}">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0" aria-hidden="true">
                <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-opacity="1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-opacity="1" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="text-[10px] font-medium truncate w-full text-center">{{ __('Explorar') }}</span>
        </a>
        <a href="{{ route('dashboard') }}" class="nav-link add-post flex flex-col items-center justify-center -mt-6 rounded-full bg-gradient-to-r from-[#2196f3] to-[#1976d2] dark:from-indigo-600 dark:to-indigo-800 w-14 h-14 text-white shadow-lg shrink-0" title="{{ __('Mi panel') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </a>
        <a href="{{ route('social-accounts.index') }}" class="nav-link flex flex-col items-center justify-center gap-0.5 min-w-0 flex-1 py-2 text-gray-500 dark:text-gray-400 hover:text-[#2196f3] dark:hover:text-indigo-400 {{ request()->routeIs('social-accounts.*') ? 'text-[#2196f3] dark:text-indigo-400' : '' }}" title="{{ __('Redes') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 512 512" class="opacity-80 shrink-0" aria-hidden="true"><path d="M436.594 74.943c-99.917-99.917-261.637-99.932-361.568 0-80.348 80.347-95.531 199.817-48.029 294.96L.662 485.742c-3.423 15.056 10.071 28.556 25.133 25.133l115.839-26.335c168.429 84.092 369.846-37.653 369.846-228.812 0-68.29-26.595-132.494-74.886-180.785zM309.143 319.394h-160c-11.598 0-21-9.402-21-21s9.402-21 21-21h160c11.598 0 21 9.402 21 21s-9.402 21-21 21zm53.334-85.333H149.143c-11.598 0-21-9.402-21-21s9.402-21 21-21h213.334c11.598 0 21 9.402 21 21s-9.403 21-21 21z"/></svg>
            <span class="text-[10px] font-medium truncate w-full text-center">{{ __('Redes') }}</span>
        </a>
        <a href="{{ route('profile.show') }}" class="nav-link flex flex-col items-center justify-center gap-0.5 min-w-0 flex-1 py-2 text-gray-500 dark:text-gray-400 hover:text-[#2196f3] dark:hover:text-indigo-400 {{ request()->routeIs('profile.show') ? 'text-[#2196f3] dark:text-indigo-400' : '' }}" title="{{ __('Perfil') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="21" fill="currentColor" viewBox="0 0 16 21" aria-hidden="true"><path d="M8 7.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 1 0 0 7.5zm7.5 9v1.5c-.002.199-.079.39-.217.532C13.61 20.455 8.57 20.5 8 20.5s-5.61-.045-7.282-1.718C.579 18.64.501 18.449.5 18.25v-1.5a7.5 7.5 0 1 1 15 0z"/></svg>
            <span class="text-[10px] font-medium truncate w-full text-center">{{ __('Perfil') }}</span>
        </a>
    </div>
</nav>
