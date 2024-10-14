<?php

namespace App\Events;

use App\Http\Resources\UserTeamResource;
use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
			'team' => UserTeamResource::collection(
				$this->relationRequest->from->teams()
					->where('teams.id', $this->relationRequest->to->team->id)
					->withPivot('role_id')
					->with([
						'detailed' => fn(MorphTo $morphTo) =>
							$morphTo->morphWith([
								Producer::class => ['logo:id,path']
							])
					])
					->get()
					->map(function(Team $team) {
						$team->permissions = [];

						return $team;
					})
			)
		];
	}
}
