<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceContract;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
	/**
	 * @param Request $request
	 * @return User[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getNonRelatedTeams(Request $request)
	{
		/** @var User $user */
		$user = auth()->user();

		return $user->nonRelatedTeams()
			->when($request->has('query'), function($query) use ($request) {
				return $query->where('display_name', 'like', '%' . $request->get('query') . '%');
			})
			->limit(25)
			->orderBy('display_name')
			->get();
	}

	/**
	 * @return \App\Models\City[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getLocation()
	{
		/** @var UserService $userService */
		$userService = app(UserServiceContract::class);
		return $userService->getUserLocationByIp();
	}
}
