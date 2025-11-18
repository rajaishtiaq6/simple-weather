<?php

namespace Shaqi\SimpleWeather\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Setting\PanelSections\SettingOthersPanelSection;

class SimpleWeatherServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/simple-weather')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->publishAssets();

        $this->app->register(HookServiceProvider::class);

        // Register settings panel section
        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('simple-weather')
                    ->setTitle(trans('plugins/simple-weather::simple-weather.settings.title'))
                    ->withIcon('ti ti-cloud')
                    ->withPriority(150)
                    ->withDescription(trans('plugins/simple-weather::simple-weather.settings.description'))
                    ->withRoute('simple-weather.settings')
            );
        });
    }
}

