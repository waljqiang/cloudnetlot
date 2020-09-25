<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AccessTokenCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The newly created token ID.
     *
     * @var string
     */
    public $tokenId;

    /**
     * The ID of the user associated with the token.
     *
     * @var string
     */
    public $userId;

    /**
     * The ID of the client associated with the token.
     *
     * @var string
     */
    public $clientId;

    /**
     * Create a new event instance.
     *
     * @param  string  $tokenId
     * @param  string  $userId
     * @param  string  $clientId
     * @return void
     */
    public function __construct($tokenId, $userId, $clientId){
        $this->userId = $userId;
        $this->tokenId = $tokenId;
        $this->clientId = $clientId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
