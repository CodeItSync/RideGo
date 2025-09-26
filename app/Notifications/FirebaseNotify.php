<?php

namespace App\Notifications;

use App\Broadcasting\FalconryChannel;
use App\Broadcasting\TaxiGoChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseNotify extends Notification
{
    use Queueable;

    protected $data;
    protected $messaging;

    public function __construct($data)
    {
        $this->data = $data;
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase-adminsdk.json'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [TaxiGoChannel::class];
    }

    public function toMobileApp($notifiable): CloudMessage
    {
        $data = $this->data;
        $title = $data['title'];
        $body = $data['body'];
        $data = $data['data'];
        $data['data'] = json_encode($data['data'] ?? []);
        $data['result'] = json_encode($data['result'] ?? []);
        $data['title'] = $title;
        $data['body'] = $body;

        $androidConfig = AndroidConfig::fromArray([
            'ttl' => '3600s',
            'priority' => 'high',
            'notification' => [
                'title' => $title,
                'body' => $body,
                'icon' => 'stock_ticker_update',
                'color' => '#f45342',
            ],
        ]);

        $apnsConfig = ApnsConfig::fromArray([
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'badge' => 42,
                ],
            ],
        ]);

        return CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withHighestPossiblePriority()
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ])
            ->withData($data)
            ->withAndroidConfig($androidConfig)
            ->withApnsConfig($apnsConfig);
    }
}
