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

	public function move(Request $request, Producer $producer, ProducerOrderService $orderService)
	{
		// todo - request
		// todo - check producer rights
		// todo - move to service

		try {
			\DB::beginTransaction();

			foreach($request->all() as $status => $orderIds) {
				// priority only for 'new' & 'process' statuses because other are affected by calendar filter + amount issue
				if (in_array($status, [Order::ORDER_STATUS_NEW, Order::ORDER_STATUS_PROCESS])) {
					ProducerOrderPriority::where('producer_id', $producer->id)
						->where('status', $status)
						->update(['order_priority' => $orderIds]);
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
