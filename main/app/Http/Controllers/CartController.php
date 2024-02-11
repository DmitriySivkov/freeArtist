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
	public function load(Request $request)
	{
		$products = $request->input('products');
		$producerIds = $request->input('producers');

		$producers = Producer::whereIn('id', $producerIds)
			->with([
				'team',
				'paymentMethods'
			])
			->get();

		$products = Product::whereIn('id', collect($products)->pluck('id'))
			->get();

		return [
			'producers' => CartCheckProducersResource::collection($producers),
			'products' => CartCheckProductsResource::collection($products),
		];
	}
}
