<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProducerUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public string $queue;

	public array $fields;

	public int $teamId;
	public int $detailedId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(array $fields, int $teamId, int $producerId)
    {
		$this->queue = config('queue.broadcast');

		$this->fields = $fields;
		$this->teamId = $teamId;
		$this->detailedId = $producerId;
    }

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('teams.' . $this->teamId);
	}

	/**
	 * @return string
	 */
	public function broadcastAs()
	{
		return 'teams.updated';
	}
}
