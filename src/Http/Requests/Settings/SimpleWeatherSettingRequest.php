<?php

namespace Shaqi\SimpleWeather\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class SimpleWeatherSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'simple_weather_default_location' => ['nullable', 'string', 'max:255'],
            'simple_weather_units' => ['required', 'in:imperial,metric'],
            'simple_weather_cache_time' => ['required', 'integer', 'min:60', 'max:86400'],
            'simple_weather_show_forecast' => [new OnOffRule()],
            'simple_weather_forecast_days' => ['required', 'integer', 'min:1', 'max:7'],
        ];
    }
}

