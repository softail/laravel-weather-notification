<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Schedule::command('app:send-warning-notifications-to-users')->withoutOverlapping();
})->dailyAt(config('app.notify_at'));
