@props(['type' => 'submit'])

<x-optional-cta type="{{ $type }}" {{ $attributes }}>
    {{ $slot }}
</x-optional-cta>
