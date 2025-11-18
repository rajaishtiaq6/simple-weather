<?php

namespace Shaqi\SimpleWeather\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use stdClass;

class SimpleWeatherHelper
{
    public static function getDefaultOptions(): array
    {
        return [
            'units' => 'imperial',
            'async' => true,
            'freq' => 0,
            'timeout' => 30,
            'console_log' => true,
        ];
    }

    public static function baseAtts(array $atts): array
    {
        $options = array_merge(
            self::getDefaultOptions(),
            [
                'units' => setting('simple_weather_units', 'imperial'),
                'freq' => (int) setting('simple_weather_freq', 0),
                'timeout' => (int) setting('simple_weather_timeout', 30),
            ]
        );

        $atts['lang'] = $atts['lang'] ?? 'en';
        $atts['units'] = $atts['units'] ?? $options['units'];
        $atts['freq'] = isset($atts['freq']) ? (int) $atts['freq'] : (int) $options['freq'];
        $atts['timeout'] = isset($atts['timeout']) ? (int) $atts['timeout'] : (int) $options['timeout'];

        return $atts;
    }

    public static function geocodeLocation(array $atts): ?array
    {
        $hash = md5(json_encode($atts['location']));
        $transient = Cache::get('sw_geocode_' . $hash);

        if (! $transient) {
            // Clean location string - remove country codes for better geocoding results
            // "Karachi, PK" -> "Karachi" or "London, UK" -> "London"
            $locationParts = explode(',', $atts['location']);
            $cityName = trim($locationParts[0]); // Get city name only

            $location = urlencode($cityName);
            $url = "https://geocoding-api.open-meteo.com/v1/search?name={$location}&count=1&language=en&format=json";

            try {
                $response = Http::timeout($atts['timeout'] ?? 30)->get($url);

                if ($response->successful()) {
                    $data = $response->json();

                    if (isset($data['results']) && ! empty($data['results'])) {
                        $locationData = $data['results'][0];
                        $transient = [
                            'lat' => $locationData['latitude'],
                            'lon' => $locationData['longitude'],
                            'name' => $locationData['name'],
                        ];
                        Cache::put("sw_geocode_$hash", $transient, now()->addDays(30));
                    }
                }
            } catch (\Exception $e) {
                return null;
            }
        }

        return $transient;
    }

    public static function getLocationByIP(): ?array
    {
        $ip = request()->ip();
        $ipTransient = 'sw_ip_' . md5($ip);
        $locationData = Cache::get($ipTransient);

        if (! $locationData) {
            try {
                $response = Http::timeout(10)->get("http://ip-api.com/json/{$ip}");

                if ($response->successful()) {
                    $ipData = $response->json();

                    if (isset($ipData['lat']) && isset($ipData['lon'])) {
                        $locationData = [
                            'lat' => $ipData['lat'],
                            'lon' => $ipData['lon'],
                        ];
                    }
                }
            } catch (\Exception $e) {
                return null;
            }
        }

        if ($locationData) {
            Cache::put($ipTransient, $locationData, now()->addHours(10));
        }

        return $locationData;
    }

    public static function checkForAutoLocation(array $atts): array
    {
        if (isset($atts['location']) && $atts['location'] === 'auto') {
            $location = self::getLocationByIP();
            if (is_array($location) && ! empty($location['lat']) && ! empty($location['lon'])) {
                $atts['latitude'] = $location['lat'];
                $atts['longitude'] = $location['lon'];
            }
        }

        if (! self::hasCoordinates($atts) && self::hasLocation($atts) && $atts['location'] !== 'auto') {
            // Use geocoding API to get coordinates from location name
            $geocode = self::geocodeLocation($atts);
            if (! empty($geocode)) {
                $atts['latitude'] = $geocode['lat'];
                $atts['longitude'] = $geocode['lon'];
            }
        }

        return $atts;
    }

    public static function hasCoordinates(array $atts): bool
    {
        return ! empty($atts['latitude']) && ! empty($atts['longitude']);
    }

    public static function hasLocation(array $atts): bool
    {
        return ! empty($atts['location']) && $atts['location'] !== 'auto';
    }

    public static function hasLocationOrCoordinates(array $atts): bool
    {
        return self::hasCoordinates($atts) || self::hasLocation($atts);
    }

    public static function getNewWeatherData(array $atts): stdClass|array
    {
        $atts = self::baseAtts($atts);
        $atts = self::checkForAutoLocation($atts);

        if (! self::hasLocationOrCoordinates($atts)) {
            return [
                'error' => true,
                'message' => trans('plugins/simple-weather::simple-weather.could_not_find_location', ['location' => $atts['location'] ?? 'unknown']),
            ];
        }

        // Create hash for caching
        $cacheKey = md5(json_encode([
            'lat' => $atts['latitude'],
            'lon' => $atts['longitude'],
            'units' => $atts['units'],
        ]));

        $transient = Cache::get('sw_data_' . $cacheKey);
        if (! $transient) {
            // Use OpenMeteo API - completely free, no API key needed
            $urlRaw = self::buildOpenMeteoUrl($atts);

            try {
                $response = Http::timeout((int) $atts['timeout'])->get($urlRaw);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data && ! isset($data['error'])) {
                        $transient = self::convertOpenMeteoData($data);

                        $freq = (int) $atts['freq'] > 5 ? (int) $atts['freq'] : 5;
                        if ($freq > 0) {
                            Cache::put("sw_data_$cacheKey", $transient, now()->addMinutes($freq));
                        }
                    }
                }
            } catch (\Exception $e) {
                return [
                    'error' => true,
                    'message' => trans('plugins/simple-weather::simple-weather.could_not_connect'),
                ];
            }
        }

        return $transient ?: [
            'error' => true,
            'message' => trans('plugins/simple-weather::simple-weather.could_not_connect'),
        ];
    }

    public static function buildOpenMeteoUrl(array $atts): string
    {
        $baseUrl = 'https://api.open-meteo.com/v1/forecast';

        $params = [
            'latitude' => $atts['latitude'],
            'longitude' => $atts['longitude'],
            'current' => 'temperature_2m,relative_humidity_2m,weather_code,cloud_cover,wind_speed_10m,wind_direction_10m',
            'daily' => 'weather_code,temperature_2m_max,temperature_2m_min',
            'timezone' => 'auto',
            'forecast_days' => max(1, (int) ($atts['days'] ?? 1)),
        ];

        // Convert units
        if ($atts['units'] === 'imperial') {
            $params['temperature_unit'] = 'fahrenheit';
            $params['wind_speed_unit'] = 'mph';
        }

        return $baseUrl . '?' . http_build_query($params);
    }

    public static function convertOpenMeteoData(array $data): stdClass
    {
        $result = new stdClass();

        // Current weather
        if (isset($data['current'])) {
            $current = $data['current'];
            $result->current = new stdClass();
            $result->current->dt = strtotime($current['time']);
            $result->current->temp = $current['temperature_2m'];
            $result->current->humidity = $current['relative_humidity_2m'];
            $result->current->clouds = $current['cloud_cover'];
            $result->current->wind_speed = $current['wind_speed_10m'];
            $result->current->wind_deg = $current['wind_direction_10m'];

            // Convert weather code to description
            $result->current->weather = [
                (object) [
                    'id' => self::convertWeatherCode($current['weather_code']),
                    'description' => self::getWeatherDescription($current['weather_code']),
                ],
            ];
        }

        // Daily forecast
        if (isset($data['daily'])) {
            $daily = $data['daily'];
            $result->daily = [];

            for ($i = 0; $i < count($daily['time']); $i++) {
                $day = new stdClass();
                $day->dt = strtotime($daily['time'][$i]);
                $day->temp = new stdClass();
                $day->temp->max = $daily['temperature_2m_max'][$i];
                $day->temp->min = $daily['temperature_2m_min'][$i];

                $day->weather = [
                    (object) [
                        'id' => self::convertWeatherCode($daily['weather_code'][$i]),
                        'description' => self::getWeatherDescription($daily['weather_code'][$i]),
                    ],
                ];

                $result->daily[] = $day;
            }
        }

        return $result;
    }

    public static function convertWeatherCode(int $code): int
    {
        // Convert OpenMeteo weather codes to OpenWeather-like IDs for icon compatibility
        $mapping = [
            0 => 800,   // Clear sky
            1 => 801,   // Mainly clear
            2 => 802,   // Partly cloudy
            3 => 803,   // Overcast
            45 => 741,  // Fog
            48 => 741,  // Depositing rime fog
            51 => 300,  // Light drizzle
            53 => 301,  // Moderate drizzle
            55 => 302,  // Dense drizzle
            56 => 311,  // Light freezing drizzle
            57 => 312,  // Dense freezing drizzle
            61 => 500,  // Slight rain
            63 => 501,  // Moderate rain
            65 => 502,  // Heavy rain
            66 => 511,  // Light freezing rain
            67 => 511,  // Heavy freezing rain
            71 => 600,  // Slight snow fall
            73 => 601,  // Moderate snow fall
            75 => 602,  // Heavy snow fall
            77 => 615,  // Snow grains
            80 => 520,  // Slight rain showers
            81 => 521,  // Moderate rain showers
            82 => 522,  // Violent rain showers
            85 => 620,  // Slight snow showers
            86 => 621,  // Heavy snow showers
            95 => 200,  // Thunderstorm
            96 => 201,  // Thunderstorm with slight hail
            99 => 202,  // Thunderstorm with heavy hail
        ];

        return $mapping[$code] ?? 800;
    }

    public static function getWeatherDescription(int $code): string
    {
        $descriptions = [
            0 => 'Clear sky',
            1 => 'Mainly clear',
            2 => 'Partly cloudy',
            3 => 'Overcast',
            45 => 'Fog',
            48 => 'Depositing rime fog',
            51 => 'Light drizzle',
            53 => 'Moderate drizzle',
            55 => 'Dense drizzle',
            56 => 'Light freezing drizzle',
            57 => 'Dense freezing drizzle',
            61 => 'Slight rain',
            63 => 'Moderate rain',
            65 => 'Heavy rain',
            66 => 'Light freezing rain',
            67 => 'Heavy freezing rain',
            71 => 'Slight snow fall',
            73 => 'Moderate snow fall',
            75 => 'Heavy snow fall',
            77 => 'Snow grains',
            80 => 'Slight rain showers',
            81 => 'Moderate rain showers',
            82 => 'Violent rain showers',
            85 => 'Slight snow showers',
            86 => 'Heavy snow showers',
            95 => 'Thunderstorm',
            96 => 'Thunderstorm with slight hail',
            99 => 'Thunderstorm with heavy hail',
        ];

        return $descriptions[$code] ?? 'Unknown';
    }
}

