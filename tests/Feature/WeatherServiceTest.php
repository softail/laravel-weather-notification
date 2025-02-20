<?php

use App\Services\OpenMeteoWeatherService;
use App\Services\WeatherAPIService;
use App\Services\WeatherBitService;
use App\Services\WeatherService;

beforeEach(function () {
    $this->coordinates = ['lat' => 40.7128, 'lon' => -74.0060];
    $this->days = 3;
});

afterEach(fn () => Mockery::close());

it('returns the average current temperature from all services', function () {
    $mockService1 = Mockery::mock(OpenMeteoWeatherService::class);
    $mockService1->shouldReceive('getCurrentTemperature')
        ->once()
        ->with($this->coordinates)
        ->andReturn(20.0);

    $mockService2 = Mockery::mock(WeatherAPIService::class);
    $mockService2->shouldReceive('getCurrentTemperature')
        ->once()
        ->with($this->coordinates)
        ->andReturn(22.0);

    $mockService3 = Mockery::mock(WeatherBitService::class);
    $mockService3->shouldReceive('getCurrentTemperature')
        ->once()
        ->with($this->coordinates)
        ->andReturn(21.0);

    $weatherService = new WeatherService($mockService1, $mockService2, $mockService3);
    $averageTemperature = $weatherService->getCurrentTemperature($this->coordinates);

    expect($averageTemperature)->toBe(21.0);
});

it('returns the average forecast from all services', function () {
    $mockService1 = Mockery::mock(OpenMeteoWeatherService::class);
    $mockService1->shouldReceive('getWeatherForecast')
        ->once()
        ->with($this->coordinates, $this->days)
        ->andReturn([
            'current' => [
                'temperature' => 1.0,
                'precipitation' => 2.0,
                'wind_speed' => 3.0,
            ],
            'uv_index' => 4.0,
            'precipitation' => 5.0,
        ]);

    $mockService2 = Mockery::mock(WeatherAPIService::class);
    $mockService2->shouldReceive('getWeatherForecast')
        ->once()
        ->with($this->coordinates, $this->days)
        ->andReturn([
            'current' => [
                'temperature' => 2.0,
                'precipitation' => 3.0,
                'wind_speed' => 4.0,
            ],
            'uv_index' => 5.0,
            'precipitation' => 6.0,
        ]);

    $mockService3 = Mockery::mock(WeatherBitService::class);
    $mockService3->shouldReceive('getWeatherForecast')
        ->once()
        ->with($this->coordinates, $this->days)
        ->andReturn([
            'current' => [
                'temperature' => 3.0,
                'precipitation' => 4.0,
                'wind_speed' => 5.0,
            ],
            'uv_index' => 6.0,
            'precipitation' => 7.0,
        ]);

    $weatherService = new WeatherService($mockService1, $mockService2, $mockService3);
    $averageForecast = $weatherService->getWeatherForecast($this->coordinates, $this->days);

    expect($averageForecast)->not()->toBeNull()
        ->and($averageForecast)->toBe([
            'current' => [
                'temperature' => 2.0,
                'precipitation' => 3.0,
                'wind_speed' => 4.0,
            ],
            'uv_index' => 5.0,
            'precipitation' => 6.0,
        ]);
});

it('returns correct average values when one service returns null', function () {
    $mockService1 = Mockery::mock(OpenMeteoWeatherService::class);
    $mockService1->shouldReceive('getWeatherForecast')
        ->once()
        ->with($this->coordinates, $this->days)
        ->andReturn([
            'current' => [
                'temperature' => 1.0,
                'precipitation' => 2.0,
                'wind_speed' => 3.0,
            ],
            'uv_index' => 4.0,
            'precipitation' => 5.0,
        ]);

    $mockService2 = Mockery::mock(WeatherAPIService::class);
    $mockService2->shouldReceive('getWeatherForecast')
        ->once()
        ->with($this->coordinates, $this->days)
        ->andReturn([
            'current' => [
                'temperature' => null,
                'precipitation' => null,
                'wind_speed' => null,
            ],
            'uv_index' => null,
            'precipitation' => null,
        ]);

    $mockService3 = Mockery::mock(WeatherBitService::class);
    $mockService3->shouldReceive('getWeatherForecast')
        ->once()
        ->with($this->coordinates, $this->days)
        ->andReturn([
            'current' => [
                'temperature' => 3.0,
                'precipitation' => 4.0,
                'wind_speed' => 5.0,
            ],
            'uv_index' => 6.0,
            'precipitation' => 7.0,
        ]);

    $weatherService = new WeatherService($mockService1, $mockService2, $mockService3);
    $averageForecast = $weatherService->getWeatherForecast($this->coordinates, $this->days);

    expect($averageForecast)->not()->toBeNull()
        ->and($averageForecast)->toBe([
            'current' => [
                'temperature' => 2.0,
                'precipitation' => 3.0,
                'wind_speed' => 4.0,
            ],
            'uv_index' => 5.0,
            'precipitation' => 6.0,
        ]);
});
