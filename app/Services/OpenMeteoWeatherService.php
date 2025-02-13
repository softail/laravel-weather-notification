<?php

namespace App\Services;

use App\Contracts\WeatherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenMeteoWeatherService implements WeatherInterface
{
    private const API_URL = 'https://api.open-meteo.com/v1/forecast';

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
                return Http::get(self::API_URL, $requestData)->body();
            });

            return data_get(json_decode($response, true), 'current.temperature_2m');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public function getWeatherForecast(array $coordinates): ?array
    {
        $requestData = [
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lon'],
            'current' => 'temperature_2m',
            'timezone' => 'auto',
            'forecast_days' => 1,
            'daily' => 'uv_index_max,precipitation_sum',
        ];

        try {
            $response = cache()->remember($coordinates['lat'].'-'.$coordinates['lon'], 0, function () use ($requestData) {
                return Http::get(self::API_URL, $requestData)->body();
            });

            return [
                data_get(json_decode($response, true), 'daily.uv_index_max.0'),
                data_get(json_decode($response, true), 'daily.precipitation_sum.0'),
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
