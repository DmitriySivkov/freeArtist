<?php

namespace App\Http\Controllers;


use App\Contracts\OrderServiceContract;
use App\Http\Requests\UserNewOrderRequest;
use App\Services\Orders\UserOrderService;

class OrderController extends Controller
{
    public function index()
    {
		/** @var UserOrderService $orderService */
		$orderService = app(OrderServiceContract::class);

        return $orderService->getOrderList();
    }

    public function store(UserNewOrderRequest $request)
	{
		$orderData = $request->validated();

		/** @var UserOrderService $orderService */
		$orderService = app(OrderServiceContract::class);

		$invalidProducts = $orderService->findInvalidProducts($orderData['order_products']);

		if ($invalidProducts->isNotEmpty()) {
			return response([
				'message' => 'Ой, кажется кто-то вас опередил',
				'invalid_items' => $invalidProducts->map(fn(\App\Models\Product $product) => [
					'id' => $product->id,
					'title' => $product->title,
					'amount' => $product->is_active ? $product->amount : 0
				])->values(),
			], 422);
		}

		try {
			\DB::beginTransaction();

			$orderService->processOrder($orderData);

			\DB::commit();
		} catch (\Throwable $e) {
			\DB::rollBack();

			return response(['message' => 'Что-то пошло не так'], 422);
		}

		return response(['message' => 'Заказ принят'], 200);
	}


}
