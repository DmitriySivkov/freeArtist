<?php

namespace App\Http\Controllers\Producer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Producer;
use App\Models\ProducerOrderPriority;
use App\Services\Orders\ProducerOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Producer $producer, ProducerOrderService $orderService)
    {
		$orderService->setProducer($producer);

        return $orderService->getOrderList();
    }

	public function move(Request $request, Producer $producer, Order $order, ProducerOrderService $orderService)
	{
		// todo - check producer rights
		$fromStatus = $request->input('from_status');
		$orderStatus = $request->input('status');
		$position = $request->input('position');

		// todo - try catch + db migration (commit/rollback)
		// todo - move to service
		$producerOrderPriority = ProducerOrderPriority::where('producer_id', $producer->id)
			->where('status', $orderStatus)
			->first();

		if ($producerOrderPriority) {
			$priority = collect($producerOrderPriority->order_priority)
				->filter(fn($orderId) => $orderId !== $order->id)
				->toArray();

			array_splice($priority, $position, 0, $order->id);

			$producerOrderPriority->order_priority = $priority;

			$producerOrderPriority->save();
		} else {
			ProducerOrderPriority::create([
				'producer_id' => $producer->id,
				'status' => $orderStatus,
				'order_priority' => [$order->id]
			]);
		}

		if ($orderStatus !== $fromStatus) {
			$fromBoard = ProducerOrderPriority::where('producer_id', $producer->id)
				->where('status', $fromStatus)
				->first();

			$fromBoard->order_priority = collect($fromBoard->order_priority)
				->filter(fn($orderId) => $orderId !== $order->id)
				->values();

			$fromBoard->save();
		}

		$order->status = $orderStatus;
		$order->save();
	}
}
