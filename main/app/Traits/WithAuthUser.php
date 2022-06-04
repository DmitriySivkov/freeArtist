<?php


namespace App\Traits;


use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

trait WithAuthUser
{
	/**
	 * @return User|null
	 */
	public function resolveUserByAuthCookie()
	{
		if (!$PAtoken = PersonalAccessToken::findToken(request()->cookie('token')))
			return null;

		return $PAtoken->tokenable;
	}
}
