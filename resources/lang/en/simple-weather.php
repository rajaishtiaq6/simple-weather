<?php

return [
    'name' => 'Simple Weather',
    'shortcode_name' => 'Simple Weather',
    'shortcode_description' => 'Display weather information for a location',

    'settings' => [
        'title' => 'Simple Weather',
        'description' => 'Configure weather display settings',
        'default_location' => 'Default Location',
        'default_location_help' => 'Default location to display weather for (e.g., "London, GB" or "New York, US")',
        'units' => 'Default Measuring Units',
        'units_metric' => 'Metric (°C)',
        'units_imperial' => 'Imperial (°F)',
        'units_help' => 'Choose between Celsius (metric) or Fahrenheit (imperial)',
        'cache_time' => 'Cache Duration (seconds)',
        'cache_time_help' => 'How long to cache weather data (minimum 60 seconds, recommended 3600)',
        'show_forecast' => 'Show Forecast',
        'show_forecast_help' => 'Display weather forecast in addition to current weather',
        'forecast_days' => 'Forecast Days',
        'forecast_days_help' => 'Number of days to show in forecast (1-7)',
    ],

    'location' => 'Location',
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',
    'days' => 'Number of Days',
    'units' => 'Units',
    'show_units' => 'Show Units',
    'show_date' => 'Show Date',
    'date_format' => 'Date Format',
    'day_name' => 'Day Name (Monday, Tuesday...)',
    'date_default' => 'Default Date Format',
    'night_mode' => 'Night Mode',
    'style' => 'Display Style',
    'title' => 'Title',
    'title_placeholder' => 'Optional title for widget style',

    'imperial' => 'Imperial (°F)',
    'metric' => 'Metric (°C)',
    'imperial_full' => 'Imperial (°F)',
    'metric_full' => 'Metric (°C)',

    'yes' => 'Yes',
    'no' => 'No',

    'default' => 'Default',
    'widget' => 'Widget',

    'could_not_find_location' => 'Could not find coordinates for location: :location. Please try a different location format.',
    'could_not_connect' => 'Could not connect to weather service',

    // Frontend messages
    'loading' => 'Loading weather...',
    'error_load_data' => 'Could not load weather data',
    'error_connect' => 'Could not connect to weather service',
    'current' => 'Current',
    'humidity' => 'Humidity',
    'clouds' => 'Clouds',
    'wind' => 'Wind',
];

