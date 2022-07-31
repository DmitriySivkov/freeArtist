<?php

namespace App\Services;

use App\Models\Producer;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProducerService
{
	/**
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function getProducersToAttach(Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();
		$user->load([
			'roles',
			'teams' => function($query) {
				$query->where('detailed_type', Producer::class);
			}
		]);

		$producers = Team::where('display_name', 'ilike', '%' . $request->get('query') . '%')
			->whereNotIn('id', $user->teams->pluck('pivot.team_id'))
			->limit(25)
			->orderBy('display_name', 'asc')
			->get();

		return response()->json($producers);
	}
}
