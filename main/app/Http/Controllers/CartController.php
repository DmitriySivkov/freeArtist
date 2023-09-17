<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartCheckProducersResource;
use App\Http\Resources\CartCheckProductsResource;
use App\Models\Producer;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
	/**
	 * @param Request $request
	 * @return array
	 */
	public function checkProducts(Request $request)
	{
		$producers = Producer::whereIn('id', $request->input('producers'))
			->with(['team'])
			->get();

		$products = Product::whereIn('id', $request->input('products'))
			->get();

		return [
			'producers' => CartCheckProducersResource::collection($producers),
			'products' => CartCheckProductsResource::collection($products),
		];
	}
}
