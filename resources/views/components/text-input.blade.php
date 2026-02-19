@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm']) }}>
