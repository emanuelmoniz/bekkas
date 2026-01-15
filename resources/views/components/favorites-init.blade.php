@php
    $favCount = (int) (Auth::check() ? Auth::user()->favorites()->count() : count(session('favorites', [])));
@endphp
<script>
    window.initialFavoritesCount = {{ $favCount }};
</script>