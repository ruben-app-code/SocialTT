@props(['name'])
@php
    $iconClass = $attributes->get('class', 'w-5 h-5');
    $attributes = $attributes->merge(['class' => 'inline-block shrink-0 ' . $iconClass]);
    $svgClass = 'feather feather-' . str_replace('_', '-', $name) . ' ' . $iconClass;
    $svgOutput = preg_replace('/\bclass="[^"]*"/', 'class="' . e($svgClass) . '"', $svg, 1);
@endphp
<span {{ $attributes }}>
    {!! $svgOutput !!}
</span>
