<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-3 bg-primary border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 focus:bg-primary/90 active:opacity-95 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
