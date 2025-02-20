<?php

namespace App\Services;

use App\Contracts\WeatherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherBitService implements WeatherInterface
{
    public function getCurrentTemperature(array $coordinates): ?float
    {
        $requestData = [
            'key' => config('weather.weather_bit.key'),
            'lat' => $coordinates['lat'],
            'lon' => $coordinates['lon'],
        ];

        try {
            $response = cache()->remember('weather-bit_'.$coordinates['lat'].'-'.$coordinates['lon'], now()->addMinutes(10), function () use ($requestData) {
                return Http::get(config('weather.weather_bit.url').'/current', $requestData)->body();
            });

            return data_get(json_decode($response, true), 'data.0.temp');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }

    public function getWeatherForecast(array $coordinates, int $days): ?array
    {
        $requestData = [
            'key' => config('weather.weather_bit.key'),
            'lat' => $coordinates['lat'],
            'lon' => $coordinates['lon'],
            'days' => $days,
        ];

        try {
            $currentResponse = cache()->remember('weather-bit_'.$coordinates['lat'].'-'.$coordinates['lon'], now()->addMinutes(10), function () use ($requestData) {
                return Http::get(config('weather.weather_bit.url').'/current', $requestData)->body();
            });

            $forecastResponse = Http::get(config('weather.weather_bit.url').'/forecast/daily', $requestData)->body();

            return [
                'current' => [
                    'temperature' => data_get(json_decode($currentResponse, true), 'data.0.temp'),
                    'precipitation' => data_get(json_decode($currentResponse, true), 'data.0.precip'),
                    'wind_speed' => data_get(json_decode($currentResponse, true), 'data.0.wind_spd'),
                ],
                'uv_index' => data_get(json_decode($forecastResponse, true), 'data.0.uv'),
                'precipitation' => data_get(json_decode($forecastResponse, true), 'data.0.precip'),
            ];
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return null;
    }
}
