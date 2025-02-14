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
            $forecast = $this->weatherService->getWeatherForecast($location->coordinates, [
                'forecast_days' => 1,
                'daily' => 'uv_index_max,precipitation_sum',
            ]);

            $maxUVIndex = data_get($forecast, 'daily.uv_index_max.0', 0);
            $precipitationSum = data_get($forecast, 'daily.precipitation_sum.0', 0);

            $isHighUV = $maxUVIndex > self::DANGER_UV_INDEX;
            $isHighPrecipitation = $precipitationSum > self::DANGER_PRECIPITATION;

            if ($isHighUV || $isHighPrecipitation) {
                $this->info('Sending notification about '.$location->name.' using: '.implode(',', $location->notify_by));
                $location->user->notify(new NotifyUserAboutWeather($location, $isHighUV, $isHighPrecipitation));
            }
        }
    }
}
