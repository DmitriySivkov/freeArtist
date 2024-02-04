<?php

namespace App\Http\Controllers\Producer;

use App\Contracts\OrderServiceContract;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Producer;
use App\Models\ProducerOrderPriority;
use App\Services\Orders\ProducerOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Producer $producer)
    {
		/** @var ProducerOrderService $orderService */
		$orderService = app(OrderServiceContract::class);

		$orderService->setProducer($producer);

        return $orderService->getOrderList();
    }

	public function move(Request $request, Producer $producer)
	{
		// todo - request
		// todo - check producer rights
		// todo - move to service

		/** @var ProducerOrderService $orderService */
		$orderService = app(OrderServiceContract::class);

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
