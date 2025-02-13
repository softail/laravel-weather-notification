<?php

namespace App\Services;

use App\Contracts\WeatherInterface;
use App\Models\Location;
use Illuminate\Support\Collection;

readonly class LocationService
{
    public function __construct(private WeatherInterface $weatherService) {}

    public function getUserLocations(): Collection
    {
        return auth()->user()?->locations->map(function ($location) {
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
