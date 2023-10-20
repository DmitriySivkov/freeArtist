<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use App\Services\Orders\ProducerOrderService;

class OrderController extends Controller
{
    public function index(Producer $producer, ProducerOrderService $orderService)
    {
		$orderService->setProducer($producer);

        return $orderService->getOrderList();
    }


}
