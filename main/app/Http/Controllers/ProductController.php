<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	/**
	 * @param ProductService $productService
	 * @return Product|\Illuminate\Http\JsonResponse
	 */
	public function store(ProductService $productService)
	{
		try {
			$productService->setProduct(new Product());

			$product = $productService->storeProduct();

			return $product->load(['images']);
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
				'images',
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
