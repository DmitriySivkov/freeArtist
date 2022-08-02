<?php

namespace App\Services\RelationRequests;

use App\Models\Producer;
use App\Models\User;

class UserRelationRequestService
{
	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getOutgoingCoworkingRequests()
	{
		/** @var User $user */
		$user = auth("sanctum")->user();

		$userRelationRequests = $user->relationRequests()
			->get();

		return $userRelationRequests->loadMorph('to', [
			Producer::class => ['team']
		]);
	}
}
