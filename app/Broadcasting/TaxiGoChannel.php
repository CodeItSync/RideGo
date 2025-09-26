<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class TaxiGoChannel
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-adminsdk.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toMobileApp($notifiable);

        if (!$message instanceof CloudMessage) {
            return;
        }

        $this->messaging->send($message);
    }
}
