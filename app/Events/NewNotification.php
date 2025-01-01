<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    public $additionalData;

    public function __construct($notification, $additionalData = [])
    {
        $this->notification = $notification;
        $this->additionalData = $additionalData;
    }

    public function broadcastWith()
    {
        return [
            'notification' => $this->notification,
            'additionalData' => $this->additionalData,
            'formattedDate' => $this->notification->created_at->diffForHumans(),
        ];
    }

    public function broadcastOn()
    {
        // Utilisation d'un canal privé pour la sécurité
        return new PrivateChannel('notifications.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        return 'NewNotification';
    }
}
