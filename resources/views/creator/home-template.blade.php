@extends('layouts.template')

@section('header_title', __('Inicio'))

@section('content')
    <div class="space-y-6">
        {{-- Zona de historias / accesos rápidos (estilo template) --}}
        <div class="flex gap-4 overflow-x-auto pb-2 -mx-4 px-4 scrollbar-hide">
            <a href="{{ route('dashboard') }}" class="flex-shrink-0 flex flex-col items-center gap-1">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#FE9063] to-[#704FFE] flex items-center justify-center text-white text-xl font-template-title font-semibold border-2 border-white dark:border-gray-800 shadow">
                    {{ Str::limit(Auth::user()->name ?? '?', 1) }}
                </div>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Tu panel') }}</span>
            </a>
            <a href="{{ route('social-accounts.index') }}" class="flex-shrink-0 flex flex-col items-center gap-1">
                <div class="w-14 h-14 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500 dark:text-gray-400 border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <x-feather-icon name="plus" class="w-6 h-6" />
                </div>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Redes') }}</span>
            </a>
            <a href="{{ route('explore') }}" class="flex-shrink-0 flex flex-col items-center gap-1">
                <div class="w-14 h-14 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <span class="text-lg font-template-title font-semibold text-gray-600 dark:text-gray-400">E</span>
                </div>
                <span class="text-xs text-gray-600 dark:text-gray-400">{{ __('Explorar') }}</span>
            </a>
        </div>

        {{-- Tarjeta de contenido de ejemplo --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="p-4">
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ __('Bienvenido al layout basado en el template.') }}</p>
                <p class="text-gray-500 dark:text-gray-500 text-xs mt-2">{{ __('Usa @extends(\'layouts.template\') y @section(\'content\') en tus vistas.') }}</p>
            </div>
        </div>
    </div>
@endsection
