<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteLocationRequest;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Services\LocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    public function __construct(private readonly LocationService $locationService) {}

    public function store(StoreLocationRequest $request): RedirectResponse
    {
        $this->locationService->handleStore($request->validated());

        return Redirect::route('dashboard');
    }

    public function show(Location $location): \Inertia\Response
    {
        return $this->locationService->handleShow($location);
    }

    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return Redirect::route('dashboard');
    }

    public function destroy(DeleteLocationRequest $request, Location $location): RedirectResponse
    {
        try {
            $location->delete();
        } catch (\Exception $e) {
            return back(Response::HTTP_INTERNAL_SERVER_ERROR)->withErrors('Error deleting location: '.$e->getMessage());
        }

        return Redirect::route('dashboard');
    }
}
