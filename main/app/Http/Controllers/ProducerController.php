<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerRegisterRequest;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Services\ProducerService;
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
			->orderBy('teams.display_name')
			->paginate($request->get('per_page'));

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
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $producer->team) &&
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

	//todo - move to service
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
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $producer->team) &&
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
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $producer->team) &&
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
			!$user->hasPermission(Permission::PERMISSION_PRODUCER_PRODUCT['name'], $producer->team) &&
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
