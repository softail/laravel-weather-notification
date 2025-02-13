<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteLocationRequest;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::query()->create([
            'user_id' => auth()->id(),
            'name' => $request->validated('name'),
            'coordinates' => $request->validated('coordinates'),
            'notify_by' => [],
        ]);

        return Redirect::route('dashboard');
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
