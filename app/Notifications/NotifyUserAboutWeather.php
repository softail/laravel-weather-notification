<?php

namespace App\Notifications;

use App\Enums\NotificationTypesEnum;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;

class NotifyUserAboutWeather extends Notification implements ShouldQueue
{
    use Queueable;

    private string $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Location $location, bool $dangerousUVIndex, bool $highPrecipitation)
    {
        $alerts = [];

        if ($highPrecipitation) {
            $alerts[] = 'high precipitation';
        }

        if ($dangerousUVIndex) {
            $alerts[] = 'dangerous UV rays';
        }

        if (!empty($alerts)) {
            $this->message = ucfirst(implode(' and ', $alerts) . " expected today in {$this->location->name}, stay safe!");
        } else {
            $this->message = "No severe weather conditions expected today in {$this->location->name}.";
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        $channels = ['mail'];

        foreach ($this->location->notify_by as $case) {
            $channels[] = getEnumCase(NotificationTypesEnum::class, $case);
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Good morning '.$notifiable->name)
            ->line($this->message);
    }

    /**
     * Get the Vonage / SMS representation of the notification.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        return (new VonageMessage)
            ->content('Good morning '.$notifiable->name.' '.$this->message)->unicode();
    }
}
