@php
    $cart = session('cart', []);
    $count = array_sum(array_column($cart, 'quantity'));
@endphp
<script>
    window.initialCartCount = {{ $count }};
</script>
