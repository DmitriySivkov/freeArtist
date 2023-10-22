<?php


namespace App\Services\Orders;

use App\Contracts\OrderServiceContract;
use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use App\Models\Producer;
use Carbon\Carbon;

class ProducerOrderService implements OrderServiceContract
{
	protected Producer $producer;

	public function setProducer(Producer $producer)
	{
		$this->producer = $producer;
	}

	public function getOrderList()
	{
		// todo - check access (middleware?)
		$date = json_decode(request()->input('date'),true);

		if (is_array($date)) {
			$dateFrom = Carbon::parse($date['from'])->startOfDay();
			$dateTo = Carbon::parse($date['to'])->endOfDay();
		} else {
			$dateFrom = Carbon::parse(request()->input('date'))->startOfDay();
			$dateTo = Carbon::parse(request()->input('date'))->endOfDay();
		}

		$query = Order::where('producer_id', $this->producer->id)
			->whereBetween('created_at', [$dateFrom, $dateTo])
			->orderBy('created_at', 'desc');

		$ordersRaw = $query->with([
			'user',
			'products'
		])->get();

		$orders = ProducerOrdersResource::collection($ordersRaw)->collection;

		return [
			Order::ORDER_STATUS_NEW => $orders->filter(fn($order) => $order->status === Order::ORDER_STATUS_NEW)->values(),
			Order::ORDER_STATUS_PROCESS => $orders->filter(fn($order) => $order->status === Order::ORDER_STATUS_PROCESS)->values(),
			Order::ORDER_STATUS_CANCEL => $orders->filter(fn($order) => $order->status === Order::ORDER_STATUS_CANCEL)->values(),
			Order::ORDER_STATUS_DONE => $orders->filter(fn($order) => $order->status === Order::ORDER_STATUS_DONE)->values(),
		];
	}

	public function processOrder($orderData)
	{
		// todo ...
	}
}
