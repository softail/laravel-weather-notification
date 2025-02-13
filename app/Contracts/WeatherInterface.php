<?php

namespace App\Contracts;

interface WeatherInterface
{
    public function getCurrentTemperature(array $coordinates): ?float;

    public function getWeatherForecast(array $coordinates): ?array;
}
