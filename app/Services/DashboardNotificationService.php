<?php

namespace App\Services;

use App\Notifications\DashboardActionNotification;
use Illuminate\Support\Collection;

class DashboardNotificationService
{
    /**
     * @param mixed $notifiable
     * @param array<string,mixed> $payload
     */
    public static function notify($notifiable, array $payload): void
    {
        if (!$notifiable) {
            return;
        }

        $notifiable->notify(new DashboardActionNotification($payload));
    }

    /**
     * @param \Illuminate\Support\Collection<int,mixed> $notifications
     * @return array<int,array<string,mixed>>
     */
    public static function toUiArray(Collection $notifications): array
    {
        return $notifications->map(function ($notification) {
            $data = is_array($notification->data) ? $notification->data : [];

            return [
                'id' => $notification->id,
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? '',
                'type' => $data['type'] ?? 'info',
                'time' => optional($notification->created_at)->format('d/m/Y H:i'),
                'read' => !is_null($notification->read_at),
                'url' => $data['url'] ?? null,
                'action_label' => $data['action_label'] ?? null,
            ];
        })->values()->all();
    }
}
