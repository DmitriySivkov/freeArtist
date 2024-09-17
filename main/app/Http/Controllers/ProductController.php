<?php

namespace App\Http\Controllers;

use App\Models\Producer;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	/**
	 * @param Producer $producer
	 * @param Request $request
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function index(Request $request, Producer $producer)
	{
		$offset = $request->input('offset');
		$limit = 5;
		$categories = $request->input('categories');

		return $producer->products()
			->with([
				'thumbnail',
				'tags'
			])
			->when($categories, function($query) use ($categories) {
				$query->whereHas('tags.categories',
					fn($query) => $query->whereIn('categories.id', $categories)
				);
			})
			->orderBy('title', 'desc')
			->offset($offset)
			->limit($limit)
			->get();
	}

	public function show(Producer $producer, Product $product)
	{
		return $product->load([
			'images',
			'thumbnail',
			'tags'
		]);
	}

	/**
	 * @param ProductService $productService
	 * @return Product|\Illuminate\Http\JsonResponse
	 */
	public function store(ProductService $productService)
	{
		try {
			$productService->setProduct(new Product());

			$product = $productService->storeProduct();

			return $product->load([
				'images' => fn($query) => $query->orderBy('created_at', 'desc')
			]);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Product $product
	 * @param ProductService $productService
	 * @return Product|\Illuminate\Http\JsonResponse
	 */
	public function update(Product $product, ProductService $productService)
	{
		try {
			$productService->setProduct($product);

			$product = $productService->updateProduct();

			return $product->load([
				'images' => fn($query) => $query->orderBy('created_at', 'desc'),
				'tags'
			]);
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}

	/**
	 * @param Product $product
	 * @param ProductService $productService
	 * @return bool|\Illuminate\Http\JsonResponse|null
	 */
	public function delete(Product $product, ProductService $productService)
	{
		try {
			$productService->setProduct($product);

			return $productService->deleteProduct();
		} catch (\Throwable $e) {
			return response()->json($e->getMessage())
				->setStatusCode(422);
		}
	}
}
