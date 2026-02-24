@php
    $cart = session('cart', []);
    $count = array_sum($cart);
@endphp
<script>
    window.initialCartCount = {{ $count }};
</script>
