<?php

namespace App\Http\Controllers;


use App\Contracts\OrderServiceContract;
use App\Http\Requests\UserNewOrderRequest;
use App\Services\Orders\UserOrderService;

class OrderController extends Controller
{
    public function index(OrderServiceContract $orderService)
    {
        return response()->json($orderService->getOrderList());
    }

    public function store(UserNewOrderRequest $request)
	{
		// todo - check cart amount
		$orderData = $request->validated();

		/** @var UserOrderService $orderService */
		$orderService = app(OrderServiceContract::class);
		// todo - try catch
		$orderService->processOrder($orderData);
	}
}
