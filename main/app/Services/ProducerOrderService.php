<?php


namespace App\Services;

use App\Contracts\OrderServiceContract;
use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use App\Models\Producer;

class ProducerOrderService implements OrderServiceContract
{
	protected Producer $producer;

	public function setProducer(Producer $producer)
	{
		$this->producer = $producer;
	}

	public function getOrderList($dateRange)
	{
		// todo - check access (middleware?)

		$orders = Order::where('producer_id', $this->producer->id)
			->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
			->with([
				'user',
				'products',
				'transaction',
			])
			->orderBy('created_at', 'desc')
			->get();

		return ProducerOrdersResource::collection($orders)->collection;
	}

	public function processOrder($orderData)
	{
		// todo ...
	}
}
