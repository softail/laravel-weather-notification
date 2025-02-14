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

    public function getWeatherForecast(array $coordinates, array $options = []): ?array
    {
        $requestData = [
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lon'],
            'current' => 'temperature_2m',
            'timezone' => 'auto',
            ...$options,
        ];

        try {
            $response = Http::get(self::API_URL, $requestData)->body();

            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
