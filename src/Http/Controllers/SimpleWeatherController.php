<?php

namespace Shaqi\SimpleWeather\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\Request;
use Shaqi\SimpleWeather\Helpers\SimpleWeatherHelper;

class SimpleWeatherController extends BaseController
{
    public function getWeather(Request $request): BaseHttpResponse
    {
        $params = $request->all();
        $weatherData = SimpleWeatherHelper::getNewWeatherData($params);

        return $this
            ->httpResponse()
            ->setData($weatherData);
    }
}

