<?php

use App\Enums\NotificationTypesEnum;
use App\Models\Location;
use App\Notifications\NotifyUserAboutWeather;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->notifyBy = getEnumCases(NotificationTypesEnum::cases());
    Notification::fake();
});

it('creates a notification message correctly when both conditions are true', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => $this->notifyBy,
    ]);

    $notification = new NotifyUserAboutWeather($location, true, true);
    $reflection = new ReflectionClass($notification);
    $messageProperty = $reflection->getProperty('message');
    $message = $messageProperty->getValue($notification);

    expect($message)->toBe('High precipitation and dangerous UV rays expected today in New York, stay safe!');
});

it('creates a notification message correctly when only dangerous UV is true', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => $this->notifyBy,
    ]);

    $notification = new NotifyUserAboutWeather($location, true, false);
    $reflection = new ReflectionClass($notification);
    $messageProperty = $reflection->getProperty('message');
    $message = $messageProperty->getValue($notification);

    expect($message)->toBe('Dangerous UV rays expected today in New York, stay safe!');
});

it('creates a notification message correctly when only high precipitation is true', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => $this->notifyBy,
    ]);

    $notification = new NotifyUserAboutWeather($location, false, true);
    $reflection = new ReflectionClass($notification);
    $messageProperty = $reflection->getProperty('message');
    $message = $messageProperty->getValue($notification);

    expect($message)->toBe('High precipitation expected today in New York, stay safe!');
});

it('returns correct notification channels based on location preferences', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => $this->notifyBy,
    ]);

    $notification = new NotifyUserAboutWeather($location, true, false);
    $channels = $notification->via();

    expect($channels)->toContain('mail')
        ->and($channels)->toContain('vonage');
});

it('formats the mail notification correctly', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => [NotificationTypesEnum::Email->name],
    ]);

    $notification = new NotifyUserAboutWeather($location, false, true);

    $mailMessage = $notification->toMail($location->user);

    expect($mailMessage)->toBeInstanceOf(MailMessage::class)
        ->and($mailMessage->greeting)->toBe('Good morning ' . $location->user->name)
        ->and($mailMessage->introLines)->toContain('High precipitation expected today in New York, stay safe!');
});

it('formats the SMS notification correctly', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => [NotificationTypesEnum::SMS->name],
    ]);

    $notification = new NotifyUserAboutWeather($location, true, false);

    $vonageMessage = $notification->toVonage($location->user);

    expect($vonageMessage)->toBeInstanceOf(VonageMessage::class)
        ->and($vonageMessage->content)->toBe('Good morning ' . $location->user->name . ' Dangerous UV rays expected today in New York, stay safe!');
});

it('creates a notification with empty message when no conditions are true', function () {
    $location = Location::factory()->create([
        'name' => 'New York',
        'notify_by' => [NotificationTypesEnum::Email->name],
    ]);

    $notification = new NotifyUserAboutWeather($location, false, false);
    $reflection = new ReflectionClass($notification);
    $messageProperty = $reflection->getProperty('message');
    $message = $messageProperty->getValue($notification);

    expect($message)->toBe('No severe weather conditions expected today in New York.');
});