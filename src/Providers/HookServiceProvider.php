<?php

namespace Shaqi\SimpleWeather\Providers;

use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Illuminate\Support\ServiceProvider;
use Shaqi\SimpleWeather\Helpers\SimpleWeatherHelper;
use Botble\Shortcode\Facades\Shortcode as ShortcodeFacade;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\NumberField;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(SHORTCODE_REGISTER_CONTENT_IN_ADMIN, [$this, 'addAdminAssets'], 12, 3);

        if (function_exists('add_shortcode')) {
            add_shortcode(
                'simple-weather',
                trans('plugins/simple-weather::simple-weather.shortcode_name'),
                trans('plugins/simple-weather::simple-weather.shortcode_description'),
                [$this, 'renderWeatherShortcode']
            );

            ShortcodeFacade::setAdminConfig('simple-weather', function (array $attributes) {
                return ShortcodeForm::createFromArray($attributes)
                    ->withLazyLoading()
                    ->add(
                        'location',
                        TextField::class,
                        TextFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.location'))
                            ->placeholder('London, GB')
                            ->defaultValue('London, GB')
                    )
                    ->add(
                        'latitude',
                        TextField::class,
                        TextFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.latitude'))
                            ->placeholder('51.5074')
                    )
                    ->add(
                        'longitude',
                        TextField::class,
                        TextFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.longitude'))
                            ->placeholder('-0.1278')
                    )
                    ->add(
                        'days',
                        NumberField::class,
                        NumberFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.days'))
                            ->defaultValue(1)
                            ->min(1)
                            ->max(7)
                    )
                    ->add(
                        'units',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.units'))
                            ->choices([
                                'imperial' => trans('plugins/simple-weather::simple-weather.imperial'),
                                'metric' => trans('plugins/simple-weather::simple-weather.metric'),
                            ])
                            ->selected($attributes['units'] ?? setting('simple_weather_units', 'imperial'))
                    )
                    ->add(
                        'show_units',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.show_units'))
                            ->choices([
                                'yes' => trans('plugins/simple-weather::simple-weather.yes'),
                                'no' => trans('plugins/simple-weather::simple-weather.no'),
                            ])
                            ->selected($attributes['show_units'] ?? 'yes')
                    )
                    ->add(
                        'show_date',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.show_date'))
                            ->choices([
                                'yes' => trans('plugins/simple-weather::simple-weather.yes'),
                                'no' => trans('plugins/simple-weather::simple-weather.no'),
                            ])
                            ->selected($attributes['show_date'] ?? 'yes')
                    )
                    ->add(
                        'date',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.date_format'))
                            ->choices([
                                'dddd' => trans('plugins/simple-weather::simple-weather.day_name'),
                                'default' => trans('plugins/simple-weather::simple-weather.date_default'),
                            ])
                            ->selected($attributes['date'] ?? 'dddd')
                    )
                    ->add(
                        'night',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.night_mode'))
                            ->choices([
                                'yes' => trans('plugins/simple-weather::simple-weather.yes'),
                                'no' => trans('plugins/simple-weather::simple-weather.no'),
                            ])
                            ->selected($attributes['night'] ?? 'no')
                    )
                    ->add(
                        'style',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(trans('plugins/simple-weather::simple-weather.style'))
                            ->choices([
                                'default' => trans('plugins/simple-weather::simple-weather.default'),
                                'widget' => trans('plugins/simple-weather::simple-weather.widget'),
                            ])
                            ->selected($attributes['style'] ?? 'default')
                    );
            });
        }
    }

    public function renderWeatherShortcode(Shortcode $shortcode): string
    {
        return view('plugins/simple-weather::shortcodes.simple-weather', compact('shortcode'))->render();
    }

    public function addAdminAssets($data, $key, $attributes): string
    {
        if ($key === 'simple-weather') {
            add_filter('shortcode_modal_footer', function ($footer) {
                return $footer . view('plugins/simple-weather::partials.shortcode-admin-note')->render();
            });
        }

        return $data;
    }
}

