<?php

use App\Contracts\WeatherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->weatherService = app(WeatherInterface::class);
});

it('can retrieve the current temperature', function () {
    $coordinates = ['lat' => 40.7128, 'lon' => -74.0060];
    $apiResponse = [
        'current' => ['temperature_2m' => 25.5],
    ];

    Http::fake([
        '*' => Http::response(json_encode($apiResponse), 200),
    ]);

    Cache::shouldReceive('remember')
        ->once()
        ->with("40.7128--74.006", \Mockery::type('DateTime'), \Mockery::on(function ($callback) {
            $responseMock = Http::get('mocked-url');
            return is_callable($callback) && $responseMock;
        }))
        ->andReturn(json_encode($apiResponse));

    $temperature = $this->weatherService->getCurrentTemperature($coordinates);

    expect($temperature)->toBe(25.5);
});


it('can retrieve the weather forecast', function () {
    $coordinates = ['lat' => 40.7128, 'lon' => -74.0060];
    $options = [
        'forecast_days' => 1,
        'daily' => 'uv_index_max,precipitation_sum',
    ];

    $apiResponse = [
        'daily' => [
            'uv_index_max' => [9.0],
            'precipitation_sum' => [11],
        ],
    ];

    Http::fake([
        '*' => Http::response(json_encode($apiResponse), 200),
    ]);

    $forecast = $this->weatherService->getWeatherForecast($coordinates, $options);

    expect($forecast)->toEqual($apiResponse);
});
