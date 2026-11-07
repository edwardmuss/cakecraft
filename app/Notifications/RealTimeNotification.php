<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class RealTimeNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $notificationData;

    /**
     * Create a new notification instance.
     *
     * @param array $data Should contain at minimum:
     *                    - title: Notification title
     *                    - body: Notification content
     *                    - user_id: Recipient user ID
     */
    public function __construct(array $data)
    {
        $this->notificationData = array_merge([
            'title' => 'New Notification',
            'body' => '',
            'user_id' => null,
            'time' => now()->toDateTimeString(),
            'type' => 'system', // 'sms', 'alert', 'message', etc.
            'link' => null,
            'icon' => 'bx bx-bell',
            'sound' => 'default',
            'priority' => 'normal' // 'high', 'normal', 'low'
        ], $data);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation for database storage.
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->notificationData['title'],
            'body' => $this->notificationData['body'],
            'user_id' => $this->notificationData['user_id'],
            'time' => $this->notificationData['time'],
            'type' => $this->notificationData['type'],
            'link' => $this->notificationData['link'],
            'icon' => $this->notificationData['icon'],
            'sound' => $this->notificationData['sound'],
            'priority' => $this->notificationData['priority'],
            'data' => $this->notificationData
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'type' => get_class($this),
            'data' => $this->toArray($notifiable),
            'read_at' => null,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->notificationData['user_id']);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'notification.created';
    }
}
