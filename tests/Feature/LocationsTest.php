<?php

use App\Enums\NotificationTypesEnum;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->user = User::factory()->create();

    Auth::login($this->user);
});

it('can store a new location', function () {
    $requestData = [
        'name' => 'Test Location',
        'coordinates' => [
            'lat' => fake()->latitude(),
            'lon' => fake()->longitude(),
        ],
    ];

    $response = $this
        ->actingAs($this->user)
        ->post(route('locations.store'), $requestData);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('locations', 1)
            ->where('locations.0.name', 'Test Location')
            ->where('locations.0.notify_by', [''])
        );
});

it('can show location', function () {
    $location = Location::factory()->create();

    $response = $this
        ->actingAs($location->user)
        ->get(route('locations.show', $location->id));

    $response->assertSessionHasNoErrors();

    $this->actingAs($location->user)
        ->get(route('locations.show', $location->id))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Location/Show')
            ->has('location')
            ->where('location.name', $location->name)
            ->has('location.notify_by')
        );
});

it('can update location', function () {
    $notificationsBy = getEnumCases(NotificationTypesEnum::cases());
    $requestData = [
        'notify_by' => $notificationsBy
    ];

    $location = Location::factory()->create();

    $response = $this
        ->actingAs($location->user)
        ->patch(route('locations.update', $location->id), $requestData);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $location->refresh();

    $this->actingAs($location->user)
        ->get('/dashboard')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('locations', 1)
            ->where('locations.0.name', $location->name)
            ->where('locations.0.notify_by', $notificationsBy)
        );
});

it('can delete location', function () {
    $location = Location::factory()->create();

    $response = $this
        ->actingAs($location->user)
        ->delete(route('locations.destroy', $location->id));

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseMissing('locations', []);

    $this->actingAs($location->user)
        ->get('/dashboard')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Dashboard')
            ->has('locations', 0)
        );
});
