@props([
    'as' => 'button',
    'href' => null,
    'type' => 'submit',
    'fullWidth' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light';
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
