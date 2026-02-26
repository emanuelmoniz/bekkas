@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-accent-primary text-start text-base font-medium text-accent-primary bg-primary/10 focus:outline-none focus:text-accent-primary focus:bg-primary/10 focus:border-accent-primary transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-grey-dark hover:text-grey-dark hover:bg-white hover:border-grey-medium focus:outline-none focus:text-grey-dark focus:bg-white focus:border-grey-medium transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
