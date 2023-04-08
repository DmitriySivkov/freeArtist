<?php

namespace App\Http\Controllers;


use App\Contracts\NewOrderRequestContract;
use App\Contracts\OrderServiceContract;

class OrderController extends Controller
{
    public function index(OrderServiceContract $orderService)
    {
        return response()->json($orderService->getOrderList());
    }

    public function store(NewOrderRequestContract $request, OrderServiceContract $orderService)
	{
		$orderData = $request->validated();

		$orderService->processOrder($orderData);
	}
}
