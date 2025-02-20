<?php

namespace App\Services;

use App\Contracts\WeatherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoWeatherService implements WeatherInterface
{
    public function getCurrentTemperature(array $coordinates): ?float
    {
        $requestData = [
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lon'],
            'current' => 'temperature_2m',
            'timezone' => 'auto',
        ];

        try {
            $response = cache()->remember($coordinates['lat'].'-'.$coordinates['lon'], now()->addMinutes(10), function () use ($requestData) {
                return Http::get(config('weather.open_meteo.url'), $requestData)->body();
            });

            return data_get(json_decode($response, true), 'current.temperature_2m');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public function getWeatherForecast(array $coordinates, int $days = 1): ?array
    {
        $requestData = [
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lon'],
            'current' => 'temperature_2m,precipitation,wind_speed_10m',
            'timezone' => 'auto',
            'daily' => 'uv_index_max,precipitation_sum',
            'forecast_days' => $days,
        ];

        try {
            $response = Http::get(config('weather.open_meteo.url'), $requestData)->body();

            return [
                'current' => [
                    'temperature' => data_get(json_decode($response, true), 'current.temperature_2m'),
                    'precipitation' => data_get(json_decode($response, true), 'current.precipitation'),
                    'wind_speed' => data_get(json_decode($response, true), 'current.wind_speed_10m'),
                ],
                'uv_index' => data_get(json_decode($response, true), 'daily.uv_index_max.0'),
                'precipitation' => data_get(json_decode($response, true), 'daily.precipitation_sum.0'),
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
