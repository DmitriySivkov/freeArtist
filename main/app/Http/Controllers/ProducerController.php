<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\Role;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function index()
	{
		$producers = Cache::remember('producerList', 300, function() {
			return Producer::orderBy('title', 'asc')
				->get();
		});

		return response()->json($producers);
	}

	/**
	 * @param Producer $producer
	 * @return JsonResponse
	 */
	public function show(Producer $producer)
	{
		return response()->json($producer->load(['products']));
	}

	/**
	 * @return JsonResponse
	 */
	public function getProducersToAttach(Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();
		$user->load(['roles', 'producers']);

		$producers = Producer::where('title', 'ilike', '%' . $request->get('query') . '%')
			->when($user->roles->contains(Role::PRODUCER), function(Builder $query) use ($user) {
				return $query->whereNotIn('id', $user->producers->pluck('id'));
			})
			->limit(25)
			->orderBy('title', 'asc')
			->get();

		return response()->json($producers);
	}
}
