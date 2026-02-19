<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-accent-primary border border-transparent rounded-md font-semibold text-xs text-light uppercase tracking-widest hover:bg-accent-primary/90 focus:bg-accent-primary/90 active:opacity-95 focus:outline-none focus:ring-2 focus:ring-accent-primary focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
