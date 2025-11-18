<?php

namespace Shaqi\SimpleWeather\Forms\Settings;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use Shaqi\SimpleWeather\Http\Requests\Settings\SimpleWeatherSettingRequest;

class SimpleWeatherSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/simple-weather::simple-weather.settings.title'))
            ->setSectionDescription(trans('plugins/simple-weather::simple-weather.settings.description'))
            ->setValidatorClass(SimpleWeatherSettingRequest::class)
            ->add(
                'simple_weather_default_location',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/simple-weather::simple-weather.settings.default_location'))
                    ->value(setting('simple_weather_default_location', 'London, GB'))
                    ->helperText(trans('plugins/simple-weather::simple-weather.settings.default_location_help'))
                    ->placeholder('e.g., London, GB or New York, US')
            )
            ->add(
                'simple_weather_units',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/simple-weather::simple-weather.settings.units'))
                    ->choices([
                        'metric' => trans('plugins/simple-weather::simple-weather.settings.units_metric'),
                        'imperial' => trans('plugins/simple-weather::simple-weather.settings.units_imperial'),
                    ])
                    ->selected(setting('simple_weather_units', 'metric'))
                    ->helperText(trans('plugins/simple-weather::simple-weather.settings.units_help'))
            )
            ->add(
                'simple_weather_cache_time',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/simple-weather::simple-weather.settings.cache_time'))
                    ->value(setting('simple_weather_cache_time', 3600))
                    ->helperText(trans('plugins/simple-weather::simple-weather.settings.cache_time_help'))
                    ->placeholder('3600')
            )
            ->add(
                'simple_weather_show_forecast',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/simple-weather::simple-weather.settings.show_forecast'))
                    ->value((bool) setting('simple_weather_show_forecast', true))
                    ->helperText(trans('plugins/simple-weather::simple-weather.settings.show_forecast_help'))
            )
            ->add(
                'simple_weather_forecast_days',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/simple-weather::simple-weather.settings.forecast_days'))
                    ->value(setting('simple_weather_forecast_days', 5))
                    ->helperText(trans('plugins/simple-weather::simple-weather.settings.forecast_days_help'))
                    ->placeholder('5')
            )
            ->add(
                'simple_weather_api_info',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/simple-weather::partials.shortcode-admin-note')->render())
            );
    }
}

