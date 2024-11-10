<?php


namespace App\Services;

use App\Contracts\OrderServiceContract;
use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use App\Models\Producer;
use Illuminate\Database\Query\JoinClause;

class ProducerOrderService implements OrderServiceContract
{
	protected Producer $producer;

	public function setProducer(Producer $producer)
	{
		$this->producer = $producer;
	}

	public function getOrderList($dateRange = null, $statuses = null)
	{
		// todo - check access (middleware?)
		return Order::where('producer_id', $this->producer->id)
			->when($dateRange, fn($query) =>
				$query->whereBetween('prepare_by', [$dateRange['start'], $dateRange['end']])
			)
			->when($statuses, fn($query) =>
				$query->whereIn('status', $statuses)
			)
			->with([
				'user',
				'assignee',
				'products',
				'transaction',
			])
			->orderBy('created_at', 'desc')
			->get();
	}

	public function getOrderListMonthly($dateRange = null, $statuses = null)
	{
		// todo - check access (middleware?)
		return Order::select([
			'orders.prepare_by',
			'orders.status',
			\DB::raw('count(*) as order_count')
		])
			->where('producer_id', $this->producer->id)
			->when($dateRange, fn($query) =>
				$query->whereBetween('prepare_by', [$dateRange['start'], $dateRange['end']])
			)
			->when($statuses, fn($query) =>
				$query->whereIn('status', $statuses)
			)
			->groupBy(['prepare_by', 'status'])
			->get();
	}

	public function processOrder($orderData)
	{
		// todo ...
	}
}
