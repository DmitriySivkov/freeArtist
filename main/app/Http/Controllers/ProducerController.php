<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerRegisterRequest;
use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use App\Services\ProducerService;
use App\Services\ResponseService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProducerController extends Controller
{
	/**
	 * @return JsonResponse
	 */
	public function index(Request $request)
	{
		$location = json_decode($request->input('location'),true);

		$producers = Producer::query()
			->select([
				'producers.*',
				'teams.display_name'
			])
			->with([
				'products' => function($query) {
					$query->with('thumbnail')->whereHas('thumbnail');
				},
				'storefrontImage'
			])
			->when((int)$request->input('range') === User::RANGE_NEARBY,
				function ($query) use ($location) {
					$query->whereHas('city', fn(Builder $builder) =>
						$builder->where('id', $location['id'])
					);
			})
			->when(
				$request->get('offset') && $request->get('offset') !== 0,
				fn(Builder $builder) => $builder->offset($request->get('offset'))
			)
			->leftJoin('teams', function(JoinClause $join) {
				$join->on('teams.detailed_id', '=', 'producers.id')
					->where('teams.detailed_type', Producer::class);
			})
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
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getProducerProducts(Producer $producer)
	{
		return $producer->products()
			->select(['id', 'title'])
			->orderBy('created_at', 'desc')
			->get();
	}

	public function getProducerProduct(Producer $producer, Product $product)
	{
		return $product->load([
			'images' => fn($query) =>
				$query->orderByDesc('created_at'),
			'thumbnail',
			'tags'
		]);
	}

	/**
	 * @param Producer $producer
	 * @param ProducerService $producerService
	 * @return \App\Models\Image|\Illuminate\Database\Eloquent\Model|JsonResponse
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
	 * @param Producer $producer
	 * @param ProducerService $producerService
	 * @return \App\Models\Image|\Illuminate\Database\Eloquent\Model|JsonResponse
	 */
	public function setProducerStorefrontImage(Producer $producer, ProducerService $producerService)
	{
		try {
			return $producerService->setStorefrontImage($producer);
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
			// todo - bring dis bullshit to common format
			return ResponseService::error($e->getMessage());
		}
	}

	/**
	 * @param Producer $producer
	 * @return \App\Models\Product[]|\Illuminate\Database\Eloquent\Collection|JsonResponse|\Illuminate\Support\Collection
	 */
	public function getProducerProductThumbnails(Producer $producer)
	{
		try {
			return $producer->products()->whereHas('thumbnail')
				->with(['thumbnail'])
				->get()
				->map(fn(\App\Models\Product $product) =>
					[
						'id' => $product['thumbnail']['id'],
						'path' => $product['thumbnail']['path']
					]
				);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}
}
