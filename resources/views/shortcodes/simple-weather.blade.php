@php
    $id = md5(json_encode($shortcode->toArray()));
    $atts = [
        'latitude' => $shortcode->latitude,
        'longitude' => $shortcode->longitude,
        'location' => $shortcode->location ?? 'London, GB',
        'days' => (int) ($shortcode->days ?? 1),
        'units' => $shortcode->units ?? setting('simple_weather_units', 'imperial'),
        'show_units' => $shortcode->show_units ?? 'yes',
        'show_date' => $shortcode->show_date ?? 'yes',
        'date' => $shortcode->date ?? 'dddd',
        'night' => $shortcode->night ?? 'no',
        'style' => $shortcode->style ?? 'default',
    ];

    $async = setting('simple_weather_async', true);

    // Preload weather if async is off
    $weatherFeed = null;
    if (!$async) {
        $weatherData = simple_weather_get_data($atts);
        if (!isset($weatherData['error'])) {
            $weatherFeed = $weatherData;
        }
    }

    // Add night mode class
    $nightClass = $atts['night'] === 'yes' ? ' simple-weather--night' : '';
@endphp

{{-- Load CSS inline --}}
<link rel="stylesheet" href="{{ asset('vendor/core/plugins/simple-weather/css/simple-weather.css') }}">

<div id="simple-weather--{{ $id }}" class="simple-weather{{ $nightClass }}" data-weather-id="{{ $id }}">
    <div class="simple-weather__loading">{{ trans('plugins/simple-weather::simple-weather.loading') }}</div>
</div>

{{-- Load JS and initialize inline --}}
<script src="{{ asset('vendor/core/plugins/simple-weather/js/simple-weather.js') }}"></script>
<script>
    // Setup global objects
    window.SimpleWeather = window.SimpleWeather || {};
    window.SimpleWeather.apiUrl = '{{ route('simple-weather.api.get-weather') }}';
    window.SimpleWeather.i18n = {
        loading: '{{ trans('plugins/simple-weather::simple-weather.loading') }}',
        errorLoadData: '{{ trans('plugins/simple-weather::simple-weather.error_load_data') }}',
        errorConnect: '{{ trans('plugins/simple-weather::simple-weather.error_connect') }}',
        current: '{{ trans('plugins/simple-weather::simple-weather.current') }}',
        humidity: '{{ trans('plugins/simple-weather::simple-weather.humidity') }}',
        clouds: '{{ trans('plugins/simple-weather::simple-weather.clouds') }}',
        wind: '{{ trans('plugins/simple-weather::simple-weather.wind') }}'
    };
    window.SimpleWeatherAtts = window.SimpleWeatherAtts || {};
    window.SimpleWeatherFeeds = window.SimpleWeatherFeeds || {};

    // Set widget data
    window.SimpleWeatherAtts['{{ $id }}'] = @json($atts);
    @if($weatherFeed)
        window.SimpleWeatherFeeds['{{ $id }}'] = @json($weatherFeed);
    @endif

    // Initialize immediately
    (function() {
        if (typeof SimpleWeatherApp !== 'undefined') {
            const atts = window.SimpleWeatherAtts['{{ $id }}'];
            const preloadedFeed = window.SimpleWeatherFeeds ? window.SimpleWeatherFeeds['{{ $id }}'] : null;
            new SimpleWeatherApp('{{ $id }}', atts, preloadedFeed);
        }
    })();
</script>

