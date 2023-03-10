<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerRegisterRequest;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\ProducerService;
use App\Services\ProductService;
use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProducerController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function index(Request $request)
	{
		$producers = Producer::select([
			'producers.*',
			'teams.display_name'
		])
			->leftJoin('teams', function(JoinClause $join) {
				$join->on('teams.detailed_id', '=', 'producers.id')
					->where('teams.detailed_type', Producer::class);
			})
			->when(
				(int)$request->get('location_range') === User::LOCATION_RANGE_NEARBY && $request->get('city'),
				fn(Builder $builder) =>
				$builder->whereHas('city', fn(Builder $builder) =>
					$builder->where('city', 'like', '%' . $request->get('city') . '%')
				)
			)
			->when(
				$request->get('offset') && $request->get('offset') !== 0,
				fn(Builder $builder) => $builder->offset($request->get('offset'))
			)
			->limit($request->get('limit'))
			->orderBy('teams.display_name')
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
	 * @param Request $request
	 * @return Product|\Illuminate\Database\Eloquent\Model
	 */
	public function storeProducerProduct(Producer $producer, Request $request)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		if (
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $producer->team) &&
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

	// todo - throw default exceptions instead of 'logic' ones everywhere
	/**
	 * @param Product $product
	 * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|null
	 */
	public function deleteProducerProduct(Product $product)
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		try {
			if (
				!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $product->producer->team) &&
				!$user->owns($product->producer->team)
			)
				throw new \Exception('Доступ закрыт');

			return $product->delete();
		} catch (\LogicException) {
			return response("Удаление не выполнено. Ошибка сервера", 422);
		} catch (\Throwable $e) {
			return response($e->getMessage(), 422);
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

	public function updateProducerProduct(Product $product, Request $request, ProductService $productService)
	{
		try {
			$productService->setProduct($product);
			$productService->syncProductCommonSettings();
			$productService->syncProductComposition();
			$productService->syncProductImages();
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Producer $producer
	 * @param ProducerService $producerService
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
	 * @param ProducerRegisterRequest $request
	 * @param ProducerService $producerService
	 * @return JsonResponse
	 */
	public function register(ProducerRegisterRequest $request, ProducerService $producerService)
	{
		try {
			return $producerService->register($request->validated());
		} catch (\Throwable $e) {
			// todo - bring errors to this view everywhere (upd: very doubtly - rather remove it)
			return ResponseService::error($e->getMessage());
		}
	}
}
