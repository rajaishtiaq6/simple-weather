<?php

use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;
use Shaqi\SimpleWeather\Http\Controllers\SimpleWeatherController;
use Shaqi\SimpleWeather\Http\Controllers\Settings\SimpleWeatherSettingController;

AdminHelper::registerRoutes(function (): void {
    Route::group(['prefix' => 'settings/simple-weather', 'as' => 'simple-weather.'], function (): void {
        Route::get('/', [SimpleWeatherSettingController::class, 'edit'])->name('settings');
        Route::put('/', [SimpleWeatherSettingController::class, 'update'])->name('settings.update');
    });
});

Route::group(['namespace' => 'Shaqi\SimpleWeather\Http\Controllers', 'middleware' => ['web', 'core']], function (): void {
    Route::group(['prefix' => 'api/simple-weather', 'as' => 'simple-weather.api.'], function (): void {
        Route::post('get-weather', [SimpleWeatherController::class, 'getWeather'])->name('get-weather');
    });
});

