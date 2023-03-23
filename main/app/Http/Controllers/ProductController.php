<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Producer;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	/**
	 * @param Producer $producer
	 * @param Request $request
	 * @return Product|\Illuminate\Database\Eloquent\Model
	 */
	public function store(Producer $producer, Request $request)
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
	 * @param Product $product
	 * @param Request $request
	 * @param ProductService $productService
	 * @return array|\Illuminate\Http\JsonResponse
	 */
	public function update(Product $product, Request $request, ProductService $productService)
	{
		$changes = json_decode($request->input('changes'),true);

		try {
			$productService->setProduct($product);

			if ($changes['common']) {
				$productService->syncProductCommonSettings();
			}

			if ($changes['composition']) {
				$productService->syncProductComposition();
			}

			if ($changes['images']) {
				$productService->syncProductImages();
			}

			return [
				'changes' => $changes,
				'product' => $productService->getProduct()->load('images')
			];

		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Product $product
	 * @return bool|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|null
	 */
	public function delete(Product $product)
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
}
