<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        return $this->orderRepository->getList();
    }
}
