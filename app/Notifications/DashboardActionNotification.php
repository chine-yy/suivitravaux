<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DashboardActionNotification extends Notification
{
    use Queueable;

    /** @var array<string,mixed> */
    protected $payload;

    /**
     * @param array<string,mixed> $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = [
            'title' => $payload['title'] ?? 'Notification',
            'message' => $payload['message'] ?? '',
            'type' => $payload['type'] ?? 'info',
            'url' => $payload['url'] ?? null,
            'action_label' => $payload['action_label'] ?? null,
            'meta' => $payload['meta'] ?? [],
        ];
    }

    /**
     * @return array<int,string>
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray($notifiable)
    {
        return $this->payload;
    }
}
