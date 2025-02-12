<?php

namespace App\Http\Controllers;

use App\Enums\NotificationTypesEnum;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $locations = auth()->user()->locations;
        $notificationTypes = getEnumCases(NotificationTypesEnum::cases());

        return Inertia::render('Dashboard', compact('locations', 'notificationTypes'));
    }
}
