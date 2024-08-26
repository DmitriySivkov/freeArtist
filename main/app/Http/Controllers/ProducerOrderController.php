<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProducerOrderRequest;
use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use App\Models\Producer;
use App\Models\ProducerOrderPriority;
use App\Services\ProducerOrderService;
use Illuminate\Http\Request;

class ProducerOrderController extends Controller
{
    public function index(Producer $producer, ProducerOrderService $orderService)
    {
		$orderService->setProducer($producer);

        return $orderService->getOrderList();
    }

	public function update(ProducerOrderRequest $request, Producer $producer, Order $order)
	{
		// todo permission "producer_order"
		$fields = $request->validated();

		$order->update($fields);

		return new ProducerOrdersResource($order);
	}

	public function move(Request $request, Producer $producer)
	{
		// todo - request
		// todo - check producer rights
		// todo - move to service

		try {
			\DB::beginTransaction();

			foreach($request->all() as $status => $orderIds) {
				// priority only for 'new' & 'process' statuses because other are affected by calendar filter + amount issue
				if (in_array($status, [Order::ORDER_STATUS_NEW, Order::ORDER_STATUS_PROCESS])) {
					$orderPriority = ProducerOrderPriority::where('producer_id', $producer->id)
						->where('status', $status)
						->first();

					if ($orderPriority) {
						$orderPriority->order_priority = $orderIds;
						$orderPriority->save();
					} else {
						ProducerOrderPriority::create([
							'producer_id' => $producer->id,
							'status' => $status,
							'order_priority' => $orderIds
						]);
					}
				}

				Order::whereIn('id', $orderIds)
					->where('status', '!=', $status)
					->update(['status' => $status]);
			}

			\DB::commit();
		} catch (\Throwable) {
			\DB::rollBack();
		}
	}
}
