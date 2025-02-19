<?php

namespace App\Services;

use App\Contracts\WeatherInterface;

class WeatherService implements WeatherInterface
{
    private array $services;

    public function __construct(OpenMeteoWeatherService $service1, WeatherAPIService $service2)
    {
        $this->services = [$service1, $service2];
    }

    public function getCurrentTemperature(array $coordinates): ?float
    {
        $temperatures = [];

        foreach ($this->services as $service) {
            $temperatures[] = $service->getCurrentTemperature($coordinates);
        }

        return (float) number_format(collect($temperatures)->avg(), 1);
    }

    public function getWeatherForecast(array $coordinates, int $days): ?array
    {
        $forecast = [];

        foreach ($this->services as $service) {
            $forecast[] = $service->getWeatherForecast($coordinates, $days);
        }

        return getAverageValues($forecast);
    }
}
