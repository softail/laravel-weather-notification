<?php

namespace App\Services;

use App\Contracts\WeatherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherAPIService implements WeatherInterface
{
    public function getCurrentTemperature(array $coordinates): ?float
    {
        $requestData = [
            'key' => config('weather.weather_api.key'),
            'q' => $coordinates['lat'].','.$coordinates['lon'],
        ];

        try {
            $response = cache()->remember('weather-api_'.$coordinates['lat'].'-'.$coordinates['lon'], now()->addMinutes(10), function () use ($requestData) {
                return Http::get(config('weather.weather_api.url'), $requestData)->body();
            });

            return data_get(json_decode($response, true), 'current.temp_c');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public function getWeatherForecast(array $coordinates, int $days): ?array
    {
        $requestData = [
            'key' => config('weather.weather_api.key'),
            'q' => $coordinates['lat'].','.$coordinates['lon'],
            'days' => $days,
        ];

        try {
            $response = Http::get(config('weather.weather_api.url'), $requestData)->body();

            return [
                'current' => [
                    'temperature' => data_get(json_decode($response, true), 'current.temp_c'),
                    'precipitation' => data_get(json_decode($response, true), 'current.precip_mm'),
                    'wind_speed' => data_get(json_decode($response, true), 'current.wind_kph'),
                ],
                'uv_index' => data_get(json_decode($response, true), 'forecast.forecastday.0.day.uv', 0),
                'precipitation' => data_get(json_decode($response, true), 'forecast.forecastday.0.day.totalprecip_mm', 0),
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
