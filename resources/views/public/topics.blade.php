@extends('layouts.guest')

@section('title', __('Temas') . ' - ' . config('app.name'))

@section('content')
<div class="max-w-5xl">
    <h1 class="text-xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-1">{{ __('Temas') }}</h1>
    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A] mb-4">{{ __('Explora creadores por categoría. Cada enlace abre el listado filtrado.') }}</p>

    @if ($topics->isEmpty())
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">{{ __('Aún no hay temas.') }}</p>
    @else
        <div class="rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] bg-white dark:bg-[#161615] overflow-hidden text-sm">
            @foreach ($topics as $topic)
                <div class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] last:border-b-0 px-3 py-2 sm:py-2.5">
                    <div class="flex flex-col sm:flex-row sm:flex-wrap sm:items-baseline gap-x-5 gap-y-1">
                        <div class="shrink-0 sm:w-44 md:w-52">
                            <a href="{{ route('explore', ['topic' => $topic->slug]) }}" class="font-semibold text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#F53003] dark:hover:text-[#FF4433] hover:underline decoration-[#F53003]/40">
                                {{ $topic->name }}
                            </a>
                            <span class="text-[#706f6c] dark:text-[#A1A09A] font-normal tabular-nums">({{ $topic->users_count }})</span>
                        </div>
                        @if ($topic->children->isNotEmpty())
                            <div class="flex-1 min-w-0 text-[13px] leading-relaxed text-[#1b1b18] dark:text-[#EDEDEC]">
                                @foreach ($topic->children as $idx => $child)
                                    @if ($idx > 0)
                                        <span class="text-[#c4c4c0] dark:text-[#5c5c58] select-none" aria-hidden="true"> · </span>
                                    @endif
                                    <a href="{{ route('explore', ['topic' => $child->slug]) }}" class="text-[#1b1b18] dark:text-[#EDEDEC] hover:text-[#F53003] dark:hover:text-[#FF4433] hover:underline underline-offset-2 decoration-[#F53003]/50">
                                        {{ $child->name }}
                                    </a>@if ($child->users_count > 0)<span class="text-[#706f6c] dark:text-[#A1A09A] tabular-nums">({{ $child->users_count }})</span>@endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <p class="mt-5 text-sm">
        <a href="{{ route('explore') }}" class="font-medium text-[#F53003] dark:text-[#FF4433] hover:underline">{{ __('Ver todos los creadores') }}</a>
    </p>
</div>
@endsection
