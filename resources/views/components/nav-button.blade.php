@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center h-full px-1 pt-1 border-b-2 border-accent-primary text-sm font-sans font-medium leading-5 text-dark focus:outline-none focus:border-accent-primary transition duration-150 ease-in-out'
            : 'inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-sm font-sans font-medium leading-5 text-grey-medium hover:text-grey-dark hover:border-grey-medium focus:outline-none focus:text-grey-dark focus:border-grey-medium transition duration-150 ease-in-out';
@endphp

<button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
    {{ $slot }}
</button>
