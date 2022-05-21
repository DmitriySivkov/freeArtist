<?php

namespace App\Http\Controllers;


use App\Contracts\Requests\NewOrderRequestContract;
use App\Contracts\Services\OrderServiceContract;

class OrderController extends Controller
{
    public function index(OrderServiceContract $orderService)
    {
        return response()->json($orderService->getList());
    }

    public function store(NewOrderRequestContract $request, OrderServiceContract $orderService)
	{
		$orderData = $request->validated();

		$orderService->processOrder($orderData);
	}
}
