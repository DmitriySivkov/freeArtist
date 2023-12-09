<?php


namespace App\Services\Orders;

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

	public function getOrderList()
	{
		// todo - check access (middleware?)
		$date = json_decode(request()->input('date'), true);
		$status = request()->input('status');

		if (is_array($date)) {
			$dateFrom = Carbon::parse($date['from'])->startOfDay();
			$dateTo = Carbon::parse($date['to'])->endOfDay();
		} else {
			$dateFrom = Carbon::parse(request()->input('date'))->startOfDay();
			$dateTo = Carbon::parse(request()->input('date'))->endOfDay();
		}

		if ($status) {
			$orders = Order::where('producer_id', $this->producer->id)
				->where('status', $status)
				->whereBetween('created_at', [$dateFrom, $dateTo])
				->with([
					'user',
					'products'
				])
				->orderBy('created_at', 'desc')
				->get();

			return ProducerOrdersResource::collection($orders)->collection;
		} else {
			$commonOrders = Order::where('producer_id', $this->producer->id)
				->whereIn('status', [Order::ORDER_STATUS_NEW, Order::ORDER_STATUS_PROCESS])
				->with([
					'user',
					'products'
				])
				->orderBy('created_at', 'desc')
				->get();

			$calendarOrders = Order::where('producer_id', $this->producer->id)
				->whereBetween('created_at', [$dateFrom, $dateTo])
				->whereIn('status', [Order::ORDER_STATUS_CANCEL, Order::ORDER_STATUS_DONE])
				->with([
					'user',
					'products'
				])
				->orderBy('created_at', 'desc')
				->get();

			$orderPriority = ProducerOrderPriority::where('producer_id', $this->producer->id)
				->get();

			$orders = ProducerOrdersResource::collection($commonOrders->merge($calendarOrders))->collection;

			return [
				Order::ORDER_STATUS_NEW => $this->getSortedOrders($orders, Order::ORDER_STATUS_NEW, $orderPriority)
					->values(),

				Order::ORDER_STATUS_PROCESS => $this->getSortedOrders($orders, Order::ORDER_STATUS_PROCESS, $orderPriority)
					->values(),

				Order::ORDER_STATUS_CANCEL => $orders->filter(fn($order) => $order->status === Order::ORDER_STATUS_CANCEL)
					->values(),

				Order::ORDER_STATUS_DONE => $orders->filter(fn($order) => $order->status === Order::ORDER_STATUS_DONE)
					->values(),
			];
		}
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
