@props(['config' => [], 'images' => null])

{{--
    Anonymous image scroller component.

    **Usage:**
      - Pass a collection/array of image URLs via `:images` if you already
        have the list you want to show.
      - Otherwise supply a `:config` array describing where to fetch images
        from; the helper `image_scroller_images()` will be run to build the
        URL list.

    The component renders a container with a `data-image-scroller`
    attribute containing a JSON‑encoded configuration.  Only the following
    keys are used by the frontend script:

      * `interval` – scroll interval in milliseconds (required, default
        `3000` if omitted).
      * `autoplay` – boolean; when `false` the scroller will not start
        automatically (default `true` if provided without value).  **If
        this key is present then `autoplay_desktop`/`autoplay_mobile` are
        ignored.**
      * `autoplay_desktop` – boolean (default `false`); if `autoplay` is not
        supplied this flag governs autoplay behaviour when the viewport is
        at or above the Tailwind `md` breakpoint.
      * `autoplay_mobile` – boolean (default `true`); similar to above but
        applies below the `md` breakpoint.

    When you call the component with `:config` the helper recognises all
    of the initial filter keys below; these are executed on the server and
    result in a final URL collection.  The scroller itself is oblivious to
    any of this – it only ever sees a flat list of URLs and the two keys
    above.

    **Config helper keys:**

      ```php
      image_scroller_images([
          // data sources (any combination allowed):
          'product'   => <single product id>,
          'products'  => [
              'ids'      => [1,2,3],          // optional list of IDs
              'featured' => true|false|null,   // include only featured/unfeatured
              'active'   => true|false|null,   // match `active` flag
              'per_item' => <max photos per product>,
          ],
          'project'   => <single project id>,
          'projects'  => [
              'ids'      => [...],
              'featured' => true|false|null,
              'active'   => true|false|null,   // uses `is_active` column
              'per_item' => <max photos per project>,
          ],

          // global options:
          'max'       => <maximum total images; null for no limit (homepage passes null)>,
          'interval'  => <ms>,              // 
          'autoplay'  => true|false,        // if set, overrides the device‑specific autoplay flags below
          'autoplay_desktop' => true|false, // disconsidered if `autoplay` is set
          'autoplay_mobile'  => true|false, // disconsidered if `autoplay` is set
      ]);
      ```

    The helper's unit tests (`tests/Unit/ImageScrollerTest.php`) contain
    further examples of valid configurations and show how filters are
    applied.  The frontend JavaScript (`resources/js/image-scroller.js`)
    simply reads `interval` and `autoplay` from the generated
    attribute.
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
        // explicit override – ignore the device‑specific flags
        $scrollerConfig['autoplay'] = (bool) $config['autoplay'];
    } else {
        // add separate desktop/mobile defaults if provided (or use
        // hardcoded defaults).  the JS will consult the md breakpoint.
        $scrollerConfig['autoplay_desktop'] =
            isset($config['autoplay_desktop'])
                ? (bool) $config['autoplay_desktop']
                : false;
        $scrollerConfig['autoplay_mobile'] =
            isset($config['autoplay_mobile'])
                ? (bool) $config['autoplay_mobile']
                : true;
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
