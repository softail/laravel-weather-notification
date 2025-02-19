<?php

namespace App\Console\Commands;

use App\Contracts\WeatherInterface;
use App\Notifications\NotifyUserAboutWeather;
use App\Services\LocationService;
use Illuminate\Console\Command;

class SendWarningNotificationsToUsers extends Command
{
    private const DANGER_UV_INDEX = 7;

    private const DANGER_PRECIPITATION = 10;

    public function __construct(
        private readonly LocationService $locationService,
        private readonly WeatherInterface $weatherService
    ) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-warning-notifications-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Warning Notifications To Users About The Weather';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach ($this->locationService->getLocationsWithNotifications() as $location) {
            $forecast = $this->weatherService->getWeatherForecast($location->coordinates, 1);

            $uvIndex = $forecast['uv_index'];
            $precipitation = $forecast['precipitation'];

            if ($this->isDangerousUVIndex($uvIndex) || $this->isHighPrecipitation($precipitation)) {
                $location->user->notify(new NotifyUserAboutWeather($location, $uvIndex, $precipitation));
            }
        }
    }

    private function isDangerousUVIndex($uvIndex): bool
    {
        return $uvIndex > self::DANGER_UV_INDEX;
    }

    private function isHighPrecipitation($precipitation): bool
    {
        return $precipitation > self::DANGER_PRECIPITATION;
    }
}
