<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light']) }}>
    {{ $slot }}
</button>
