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
		$offset = $request->input('offset');
		$limit = 5;
		$categories = $request->input('categories');

		$producers = Producer::query()
			->select([
				'producers.*',
				'teams.display_name'
			])
			->with([
				'products' => function($query) use ($categories) {
					$query->with('thumbnail')
						->whereHas('thumbnail')
						->when($categories, function(Builder $query) use ($categories) {
							$query->whereHas('tags.categories',
								fn(Builder $builder) => $builder->whereIn('categories.id', $categories)
							);
						});
				},
				'logo'
			])
			->whereHas('products.images')
			->when((int)$request->input('range') === User::RANGE_NEARBY,
				function (Builder $query) use ($location) {
					$query->whereHas('city', fn(Builder $builder) =>
						$builder->where('id', $location['id'])
					);
				}
			)
			->when($offset && $offset !== 0,
				fn(Builder $builder) => $builder->offset($offset)
			)
			->when(
				$categories,
				function(Builder $query) use ($categories) {
					$query->whereHas('products.tags.categories', fn(Builder $builder) =>
						$builder->whereIn('categories.id', $categories)
					);
				}
			)
			->leftJoin('teams', function(JoinClause $join) {
				$join->on('teams.detailed_id', '=', 'producers.id')
					->where('teams.detailed_type', Producer::class);
			})
			->limit($limit)
			->orderBy('teams.display_name')
			->get();

		return response()->json($producers);
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
	 * @return array|JsonResponse
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
	 * @return array|JsonResponse
	 */
	public function register(ProducerRegisterRequest $request, ProducerService $producerService)
	{
		try {
			return $producerService->register($request->validated());
		} catch (\Throwable $e) {
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
