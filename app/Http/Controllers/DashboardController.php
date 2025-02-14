<?php

namespace App\Http\Controllers;

use App\Enums\NotificationTypesEnum;
use App\Services\LocationService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly LocationService $locationService) {}

    public function index(): Response
    {
        $notificationTypes = getEnumCases(NotificationTypesEnum::cases());
        $locations = $this->locationService->getUserLocations();

        return Inertia::render('Dashboard', compact('locations', 'notificationTypes'));
    }
}
