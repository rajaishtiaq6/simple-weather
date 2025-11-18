<?php

namespace Shaqi\SimpleWeather\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use Shaqi\SimpleWeather\Forms\Settings\SimpleWeatherSettingForm;
use Shaqi\SimpleWeather\Http\Requests\Settings\SimpleWeatherSettingRequest;

class SimpleWeatherSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/simple-weather::simple-weather.settings.title'));

        return SimpleWeatherSettingForm::create()->renderForm();
    }

    public function update(SimpleWeatherSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}

