<?php

namespace App\Services;

use App\Contracts\WeatherInterface;
use App\Models\Location;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class LocationService
{
    public function __construct(private readonly WeatherInterface $weatherService) {}

    public function handleStore(array $data): void
    {
        Location::query()->create([
            'user_id' => auth()->id(),
            'name' => data_get($data, 'name'),
            'coordinates' => data_get($data, 'coordinates'),
            'notify_by' => [],
        ]);
    }

    public function handleShow(Location $location): Response
    {
        $forecast = $this->weatherService->getWeatherForecast($location->coordinates, request()->get('days', 1));

        return Inertia::render('Location/Show', compact('location', 'forecast'));
    }

    public function getUserLocations(): Collection
    {
        return auth()->user()?->locations()->oldest()->get()->map(function ($location) {
            return [
                ...$location->toArray(),
                'current_temperature' => $this->weatherService->getCurrentTemperature($location->coordinates),
            ];
        }) ?? collect();
    }

    public function getLocationsWithNotifications(): Collection
    {
        return Location::query()
            ->with('user:id,name,email')
            ->hasNotifyBy()
            ->get();
    }
}
