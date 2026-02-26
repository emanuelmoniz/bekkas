<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-8 py-3 bg-white border border-grey-medium rounded-full font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-white focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
