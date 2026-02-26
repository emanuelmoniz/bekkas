<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-3 bg-grey-light border border-transparent rounded-full font-semibold text-xs text-grey-dark uppercase tracking-widest hover:bg-grey-light/90 active:opacity-90 focus:outline-none focus:ring-2 focus:ring-grey-medium focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
