<?php

namespace App\Services\RelationRequests;

use App\Models\Producer;
use App\Models\RelationRequest;
use App\Models\User;

class UserRelationRequestService
{
	protected User $user;

	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getOutgoingCoworkingRequests()
	{
		return $this->user->outgoingRelationRequests()
			->where('status', '!=', RelationRequest::STATUS_ACCEPTED['id'])
			->get()
			->loadMorph('to', [
				Producer::class => ['team']
			]);
	}

	public function setUser(User $user)
	{
		$this->user = $user;
	}
}
