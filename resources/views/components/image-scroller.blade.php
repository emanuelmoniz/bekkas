@props(['config' => [], 'images' => null])

{{--
    Anonymous image scroller component.

    Accepts either:
      - `:images` - a collection/array of fully-qualified URLs, or
      - `:config`  - the original configuration object used by the
        class‑based component (interval, max, products/projects filters).

    When `images` is omitted the helper `image_scroller_images()` is invoked
    to build the list from the supplied configuration.  The component only
    needs to know the final URL list and the scroll interval.
--}}

@php
    // interval is always present; other options may be passed through
    $interval = isset($config['interval']) ? (int) $config['interval'] : 3000;

    if (is_null($images)) {
        $images = image_scroller_images($config);
    } else {
        $images = collect($images);
    }

    // build final config that goes to the HTML attribute
    $scrollerConfig = ['interval' => $interval];
    if (array_key_exists('autoplay', $config)) {
        $scrollerConfig['autoplay'] = (bool) $config['autoplay'];
    }
@endphp

<div {{ $attributes->merge(['class' => 'image-scroller relative overflow-hidden']) }}
     data-image-scroller="{{ json_encode($scrollerConfig) }}">
    <div class="scroller flex h-full">
        @foreach($images as $url)
            <div class="slide flex-shrink-0 w-full h-full bg-cover bg-center"
                 style="background-image:url('{{ $url }}')"></div>
        @endforeach
    </div>
</div>
