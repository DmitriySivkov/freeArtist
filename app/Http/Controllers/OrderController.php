<?php

namespace App\Http\Controllers;


use App\Contracts\Services\OrderServiceContract;

class OrderController extends Controller
{
    public function index(OrderServiceContract $orderService)
    {
        return response()->json($orderService->getList());
    }
}
