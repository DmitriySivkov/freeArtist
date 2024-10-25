<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerOrderRequest;
use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use App\Models\Producer;
use App\Services\ProducerOrderService;

class ProducerOrderController extends Controller
{
    public function index(Producer $producer, ProducerOrderService $orderService)
    {
		$dateRange = json_decode(request()->input('dateRange'),true);

		$orderService->setProducer($producer);

        return $orderService->getOrderList($dateRange);
    }

	public function update(ProducerOrderRequest $request, Producer $producer, Order $order)
	{
		// todo permission "producer_order"
		$fields = $request->validated();

		$order->update($fields);

		return new ProducerOrdersResource($order);
	}
}
