<?php

namespace App\Events;

use App\Models\RelationRequest;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamAcceptUserRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public string $queue;

	private RelationRequest $relationRequest;
	private User $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RelationRequest $relationRequest, User $user)
    {
		$this->queue = config('queue.broadcast');

		$this->relationRequest = $relationRequest;
		$this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("relation-requests.user.{$this->user->id}");
    }

	/**
	 * @return string
	 */
	public function broadcastAs()
	{
		return 'user-team.relation-request.accepted';
	}

	public function broadcastWith()
	{
		return [
			'team' => $this->relationRequest->to->team
		];
	}
}
