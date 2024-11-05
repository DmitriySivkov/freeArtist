<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerOrderRequest;
use App\Http\Resources\ProducerOrdersResource;
use App\Http\Resources\ProducerUserResource;
use App\Models\Order;
use App\Models\Producer;
use App\Services\ProducerOrderService;
use Illuminate\Http\Request;

class ProducerOrderController extends Controller
{
    public function index(Request $request, Producer $producer, ProducerOrderService $orderService)
    {
		$isInitializing = $request->input('isInitializing');
		$dateRange = json_decode($request->input('dateRange'),true);

		$orderService->setProducer($producer);

		if (!$isInitializing) {
			return ProducerOrdersResource::collection($orderService->getOrderList($dateRange))
				->collection;
		}

        return [
			'orders'		=> ProducerOrdersResource::collection(
				$orderService->getOrderList(
					$dateRange, [Order::ORDER_STATUS_PROCESS, Order::ORDER_STATUS_CANCEL, Order::ORDER_STATUS_DONE]
				)
			)->collection,
			'assignees'		=> ProducerUserResource::collection($producer->team->users)->collection,
			'new_orders'	=> ProducerOrdersResource::collection(
				$orderService->getOrderList(null, [Order::ORDER_STATUS_NEW])
			)->collection,
		];
    }

	public function update(ProducerOrderRequest $request, Producer $producer, Order $order)
	{
		// todo permission "producer_order"
		$fields = $request->validated();

		$order->update($fields);

		return new ProducerOrdersResource($order);
	}
}
