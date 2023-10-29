<?php

namespace App\Services;

use App\Contracts\UserServiceContract;
use App\Models\City;
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
	 * @return City[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getUserLocationByIp()
	{
		info(request()->ip());
		$ip = app()->env === 'local' ? env('LOCAL_IP') : request()->ip();

		info($ip);

		// todo - what if city is not found
		$cityIpData = SxGeo::getCity($ip);

		info(print_r($cityIpData));

		// todo - maybe change 'address' to 'city' - though some cities are 'null' in db
		return City::where('address', 'like', "%{$cityIpData['city']['name_ru']}%")
			->select(['id', 'address'])
			->get();
	}
}
