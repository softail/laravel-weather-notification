<?php

use App\Contracts\WeatherInterface;
use App\Services\WeatherBitService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    app()->bind(WeatherInterface::class, WeatherBitService::class);

    $this->weatherService = app(WeatherInterface::class);
});

it('can retrieve the current temperature', function () {
    $coordinates = ['lat' => 40.7128, 'lon' => -74.0060];
    $apiResponse = [
        'data' => [
            ['temp' => 25.5],
        ],
    ];

    Http::fake([
        '*' => Http::response(json_encode($apiResponse), 200),
    ]);

    Cache::shouldReceive('remember')
        ->once()
        ->with('weather-bit_40.7128--74.006', \Mockery::type('DateTime'), \Mockery::on(function ($callback) {
            $responseMock = Http::get('mocked-url');

            return is_callable($callback) && $responseMock;
        }))
        ->andReturn(json_encode($apiResponse));

    $temperature = $this->weatherService->getCurrentTemperature($coordinates);

    expect($temperature)->toBe(25.5);
});

it('can retrieve the weather forecast', function () {
    $coordinates = ['lat' => 40.7128, 'lon' => -74.0060];
    $days = 1;

    $apiResponse = [
        'data' => [
            [
                'uv' => [9.0],
                'precip' => [11],
            ],
        ],
    ];

    Http::fake([
        '*' => Http::response(json_encode($apiResponse), 200),
    ]);

    $forecastData = $this->weatherService->getWeatherForecast($coordinates, $days);

    expect($forecastData['uv_index'])->toEqual(data_get($apiResponse, 'data.0.uv'))
        ->and($forecastData['precipitation'])->toEqual(data_get($apiResponse, 'data.0.precip'));
});
