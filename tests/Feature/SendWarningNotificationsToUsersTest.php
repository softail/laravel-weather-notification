<?php

use App\Console\Commands\SendWarningNotificationsToUsers;
use App\Contracts\WeatherInterface;
use App\Enums\NotificationTypesEnum;
use App\Models\Location;
use App\Notifications\NotifyUserAboutWeather;
use App\Services\LocationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

const SAFE_UV_INDEX = 5;
const DANGEROUS_UV_INDEX = 8;
const SAFE_PRECIPITATION = 5;
const DANGEROUS_PRECIPITATION = 12;

beforeEach(function () {
    Notification::fake();

    $this->locationService = Mockery::mock(LocationService::class);
    $this->weatherService = Mockery::mock(WeatherInterface::class);

    App::instance(LocationService::class, $this->locationService);
    App::instance(WeatherInterface::class, $this->weatherService);

    $this->command = new SendWarningNotificationsToUsers($this->locationService, $this->weatherService);
});

it('sends a weather warning notification when conditions are dangerous', function () {
    $location = Location::factory()->create([
        'notify_by' => [NotificationTypesEnum::Email->name],
    ]);

    $this->locationService->shouldReceive('getLocationsWithNotifications')
        ->once()
        ->andReturn(collect([$location]));

    $this->weatherService->shouldReceive('getWeatherForecast')
        ->once()
        ->with($location->coordinates, 1)
        ->andReturn(['uv_index' => DANGEROUS_UV_INDEX, 'precipitation' => DANGEROUS_PRECIPITATION]);

    $this->command->handle();

    $notification = new NotifyUserAboutWeather($location, true, true);
    $reflection = new ReflectionClass($notification);
    $property = $reflection->getProperty('location');

    expect($property->getValue($notification))->toBe($location);
});

it('does not send a notification when conditions are safe', function () {
    $location = Location::factory()->create([
        'notify_by' => [NotificationTypesEnum::Email->name],
    ]);

    $this->locationService->shouldReceive('getLocationsWithNotifications')
        ->once()
        ->andReturn(collect([$location]));

    $this->weatherService->shouldReceive('getWeatherForecast')
        ->once()
        ->with($location->coordinates, 1)
        ->andReturn(['uv_index' => SAFE_UV_INDEX, 'precipitation' => SAFE_PRECIPITATION]);

    $this->command->handle();

    Notification::assertNothingSent();
});
