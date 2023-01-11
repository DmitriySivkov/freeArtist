<?php

namespace App\Events;

use App\Models\Team;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class UserPermissionsSynchronized implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public string $queue;

	public User $user;
	public Team $team;
	public Collection | array $permissions;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Team $team, Collection $permissions)
    {
        $this->queue = config('queue.broadcast');

		$this->user = $user;
		$this->team = $team;
		$this->permissions = $permissions->toArray();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('permissions.' . $this->team->id);
    }

	/**
	 * @return string
	 */
	public function broadcastAs()
	{
		return 'permissions.synced';
	}
}
