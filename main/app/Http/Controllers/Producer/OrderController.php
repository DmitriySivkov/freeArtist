<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Producer;
use App\Services\Orders\ProducerOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Producer $producer, ProducerOrderService $orderService)
    {
		$orderService->setProducer($producer);

        return $orderService->getOrderList();
    }

	public function changeStatus(Request $request, Producer $producer, Order $order, ProducerOrderService $orderService)
	{
		// todo - check producer rights
		$orderStatus = $request->input('status');

		$order->status = $orderStatus;
		$order->save();
	}
}
