<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncProducerProductCommonSettingsRequest;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RelationRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Permissions\ProducerPermissionService;
use App\Services\RelationRequests\ProducerRelationRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProducerController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function index()
	{
		$producers = Producer::with(['team'])
			->get();

		return response()->json($producers);
	}

	/**
	 * @param Producer $producer
	 * @return JsonResponse
	 */
	public function show(Producer $producer)
	{
		return response()->json($producer->load(['products', 'team']));
	}

	/**
	 * @param Producer $producer
	 * @param ProducerRelationRequestService $prrService
	 * @return JsonResponse
	 */
	public function getIncomingRelationRequests(Producer $producer, ProducerRelationRequestService $prrService)
	{
		$prrService->setProducer($producer);

		return response()->json([
			'incoming_coworking_requests' => $prrService->getIncomingCoworkingRequests()
		]);
	}

	/**
	 * @param Request $request
	 * @param Producer $producer
	 * @return JsonResponse
	 */
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

	/**
	 * @param RelationRequest $relationRequest
	 * @param ProducerRelationRequestService $prrService
	 * @return RelationRequest|JsonResponse
	 */
	public function acceptCoworkingRequest(RelationRequest $relationRequest, ProducerRelationRequestService $prrService)
	{
		try {
			/** @var Producer $producer */
			$producer = $relationRequest->to;
			$prrService->setProducer($producer);
			return $prrService->acceptCoworkingRequest($relationRequest);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @param ProducerRelationRequestService $prrService
	 * @return RelationRequest|JsonResponse
	 */
	public function rejectCoworkingRequest(RelationRequest $relationRequest, ProducerRelationRequestService $prrService)
	{
		try {
			/** @var Producer $producer */
			$producer = $relationRequest->to;
			$prrService->setProducer($producer);
			return $prrService->rejectCoworkingRequest($relationRequest);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Producer $producer
	 * @return User[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
	 */
	public function getProducerUsers(Producer $producer)
	{
		return User::whereRoleIs(Role::ROLE_PRODUCER['name'], $producer->team)
			->with([
				'permissions' => function($query) use ($producer) {
					$query->where('team_id', $producer->team->id);
				}
			])
			->get();
	}

	/**
	 * @param Request $request
	 * @param Producer $producer
	 * @param User $user
	 * @param ProducerPermissionService $ppService
	 * @return JsonResponse|Collection
	 */
	public function syncUserPermissions(Request $request, Producer $producer, User $user, ProducerPermissionService $ppService)
	{
		try {
			return $ppService->syncUserPermissions($request->all(), $producer, $user);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Producer $producer
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getProducerProducts(Producer $producer)
	{
		return $producer->products()
			->with(['images' => function($query) {
				$query->orderByDesc('created_at');
			}])
			->orderByDesc('title')
			->get();
	}

	//todo - put product actions somewhere else & request validation ?

	/**
	 * @param Producer $producer
	 * @param Product $product
	 * @param Request $request
	 * @return ProductImage|\Illuminate\Database\Eloquent\Model
	 */
	public function addProducerProductImage(Producer $producer, Product $product, Request $request)
	{
		$path = Storage::disk('public')->putFile(
			'product_images',
			$request->file('image')
		);
		$productImage = ProductImage::create([
			'product_id' => $product->id,
			'path' => $path
		]);

		return $productImage;
	}

	/**
	 * @param Product $product
	 * @param Request $request
	 * @return void
	 */
	public function syncProducerProductCommonSettings(Producer $producer, Product $product, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		// TODO - add permissions
		if (!$user->owns($producer->team))
			throw new \LogicException('Доступ закрыт');

		$product->update([
			'title' => $request->input('settings.title'),
			'price' => $request->input('settings.price'),
			'amount' => $request->input('settings.amount')
		]);
	}


	/**
	 * @param Producer $producer
	 * @param Product $product
	 * @param Request $request
	 * @return array
	 */
	public function syncProducerProductCompositionSettings(Producer $producer, Product $product, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		// TODO - add permissions
		if (!$user->owns($producer->team))
			throw new \LogicException('Доступ закрыт');

		$composition = array_values(
			collect($request->get('composition'))
				->filter(function($ingredient) {
					return !array_key_exists("to_delete", $ingredient);
				})
				->map(function($ingredient) {
					return [
						"name" => $ingredient["name"],
						"description" => $ingredient["description"]
					];
				})
				->toArray()
		);

		$product->update([
			'composition' => $composition
		]);

		return $composition;
	}
}
