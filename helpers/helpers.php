<?php

use Shaqi\SimpleWeather\Helpers\SimpleWeatherHelper;

if (! function_exists('simple_weather_get_data')) {
    function simple_weather_get_data(array $atts)
    {
        return SimpleWeatherHelper::getNewWeatherData($atts);
    }
}

