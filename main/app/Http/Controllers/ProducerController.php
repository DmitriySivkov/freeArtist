<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use App\Models\RelationRequest;
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
	 * @param Request $request
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

	public function sendProducerPartnershipRequest(Request $request, Producer $producer)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();
		/** @var Producer|null $ownProducer */
		$ownProducer = $user->ownProducer();
		try {
			if ($ownProducer->id !== $producer->id)
				throw new \LogicException('Вы не являетесь владельцем этого изготовителя');

			$relationRequest = $ownProducer->outgoingProducerPartnershipRequests()
				->where('to_id', $request->get('producer'))
				->first();

			if ($relationRequest) {
				switch ($relationRequest->status['id']) {
					case RelationRequest::STATUS_PENDING['id']:
						throw new \LogicException('Запрос обрабатывается изготовителем');
					case RelationRequest::STATUS_ACCEPTED['id']:
						throw new \LogicException('Вы уже являетесь партнёром этого изготовителя');
				}
			}

			$joinRequest = RelationRequest::create([
				'from_id' => $ownProducer->id,
				'from_type' => Producer::class,
				'to_id' => $request->get('producer'),
				'to_type' => Producer::class,
				'status' => RelationRequest::STATUS_PENDING['id'],
				'message' => $request->get('message')
			]);
		} catch (\Throwable $e) {
			return response()->json(["errors" =>
				["joinProducerRequest" => [$e->getMessage()]]
			])
				->setStatusCode(422);
		}

		return response()->json($joinRequest->load(['from', 'to']));
	}
}
