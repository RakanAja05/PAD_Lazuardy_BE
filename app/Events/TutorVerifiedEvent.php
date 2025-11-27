<?php

namespace App\Events;

use App\Models\TutorConfirm;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TutorVerifiedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TutorConfirm $tutorConfirm;

    /**
     * Create a new event instance.
     */
    public function __construct(TutorConfirm $tutorConfirm)
    {
        $this->tutorConfirm = $tutorConfirm;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
