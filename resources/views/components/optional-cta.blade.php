@props([
    'as' => 'button',
    'href' => null,
    'type' => 'submit',
    'fullWidth' => false,
])

@php
    $baseClasses = 'block text-center bg-grey-light hover:bg-grey-light text-grey-dark text-sm font-normal px-8 py-3 rounded-full uppercase transition';
    $classes = $fullWidth ? "w-full {$baseClasses}" : $baseClasses;
@endphp

@if ($as === 'a')
    <a @if(!is_null($href)) href="{{ $href }}" @endif {{ $attributes->class([$classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>
        {{ $slot }}
    </button>
@endif
