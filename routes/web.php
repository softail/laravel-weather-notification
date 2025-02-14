<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->id()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware('auth')->group(function () {
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::controller(LocationController::class)->group(function () {
        Route::post('/locations', 'store')->name('locations.store');
        Route::get('/locations/{location}', 'show')->name('locations.show');
        Route::patch('/locations/{location}', 'update')->name('locations.update');
        Route::delete('/locations/{location}', 'destroy')->name('locations.destroy');
    });
});

require __DIR__.'/auth.php';
