<?php

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
