<?php

namespace App\Services;

use App\Contracts\UserServiceContract;
use App\Models\User;
use Eseath\SxGeo\Facades\SxGeo;

class UserService implements UserServiceContract
{
	protected User $user;

	/**
	 * @param User $user
	 * @return void
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function getUserOutgoingRequests()
	{
		return $this->user->outgoingRelationRequests();
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function getUserIncomingRequests()
	{
		return $this->user->incomingRelationRequests();
	}

	/**
	 * @return array|false
	 */
	public function getUserGeoByIp()
	{
		$ip = app()->env === 'local' ? env('LOCAL_IP') : request()->ip();

		return SxGeo::getCityFull($ip);
	}
}
