<?php

namespace App\Services;

use App\Contracts\UserServiceContract;
use App\Models\User;

class UserService implements UserServiceContract
{
	protected User $user;

	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getUserOutgoingRequests()
	{
		return $this->user->outgoingRelationRequests()->get();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getUserIncomingRequests()
	{
		return $this->user->incomingRelationRequests()->get();
	}
}
