@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-grey-dark']) }}>
    {{ $value ?? $slot }}
</label>
