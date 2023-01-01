<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerRegisterRequest;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RelationRequest;
use App\Models\User;
use App\Services\Permissions\ProducerPermissionService;
use App\Services\Producers\ProducerService;
use App\Services\ResponseService;
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
	 * @param ProducerService $producerService
	 * @return JsonResponse
	 */
	public function getIncomingRelationRequests(Producer $producer, ProducerService $producerService)
	{
		$producerService->setProducer($producer);

		return response()->json([
			'incoming_coworking_requests' => $producerService->getIncomingCoworkingRequests()
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
	 * @param ProducerService $producerService
	 * @return RelationRequest|JsonResponse
	 */
	public function acceptCoworkingRequest(RelationRequest $relationRequest, ProducerService $producerService)
	{
		try {
			/** @var Producer $producer */
			$producer = $relationRequest->to;
			$producerService->setProducer($producer);
			return $producerService->acceptCoworkingRequest($relationRequest);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param RelationRequest $relationRequest
	 * @param ProducerService $producerService
	 * @return RelationRequest|JsonResponse
	 */
	public function rejectCoworkingRequest(RelationRequest $relationRequest, ProducerService $producerService)
	{
		try {
			/** @var Producer $producer */
			$producer = $relationRequest->to;
			$producerService->setProducer($producer);
			return $producerService->rejectCoworkingRequest($relationRequest);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Producer $producer
	 * @param Request $request
	 * @return Product|\Illuminate\Database\Eloquent\Model
	 */
	public function storeProducerProduct(Producer $producer, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_CREATE_PRODUCT['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		return Product::create([
			'producer_id' => $producer->id,
			'title' => $request->input('settings.title'),
			'price' => $request->input('settings.price'),
			'amount' => $request->input('settings.amount') ?? 0
		]);
	}

	/**
	 * @param Producer $producer
	 * @param Product $product
	 * @return void
	 */
	public function deleteProducerProduct(Producer $producer, Product $product)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_DELETE_PRODUCT['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$product->delete();
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
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_PRODUCT['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
			throw new \LogicException('Доступ закрыт');

		$basePath = 'team_' . $producer->team->id . '/product_images';
		if ($request->hasFile('image')) {
			$path = Storage::disk('public')->putFile(
				$basePath,
				$request->file('image')
			);
		} else {
			@list($type, $fileData) = explode(';', $request->getContent());
			@list(, $fileData) = explode(',', $fileData);

			$extension = explode('/', $type);
			$path = $basePath . '/' . \Str::random(15) . '.' . end($extension);

			Storage::disk('public')->put(
				$path,
				base64_decode($fileData)
			);
		}

		return ProductImage::create([
			'product_id' => $product->id,
			'path' => $path
		]);
	}

	/**
	 * @param Producer $producer
	 * @return string
	 */
	public function setProducerLogo(Producer $producer, ProducerService $producerService)
	{
		try {
			return $producerService->setLogo($producer);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
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

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_PRODUCT['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
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

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_MANAGE_PRODUCT['name'], $producer->team) &&
			!$user->owns($producer->team)
		)
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

	public function register(ProducerRegisterRequest $request, ProducerService $producerService)
	{
		try {
			return $producerService->register($request->validated());
		} catch (\Throwable $e) {
			// todo - bring errors to this view everywhere
			return ResponseService::error($e->getMessage());
		}
	}
}
