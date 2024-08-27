<?php


namespace App\Services;

use App\Contracts\OrderServiceContract;
use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use App\Models\Producer;
use App\Models\ProducerOrderPriority;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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

	private function getSortedOrders(Collection $orders, int $status, Collection $priority)
	{
		$priority = $priority->first(fn(ProducerOrderPriority $priority) => $priority->status === $status);

		if (!$priority) {
			return collect([]);
		}

		return $orders->filter(fn($order) => $order->status === $status)
			->sort(function($a, $b) use ($priority) {
				$flipped = array_flip($priority->order_priority);

				$leftPos = $flipped[$a->id];
				$rightPos = $flipped[$b->id];
				return $leftPos >= $rightPos;
		});
	}
}
