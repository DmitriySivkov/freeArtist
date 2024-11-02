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

	public function getOrderList($dateRange)
	{
		// todo - check access (middleware?)

		return Order::select([
				'orders.*',
				'producer_order_ids.producer_order_id as producer_order_id'
			])
			->leftJoinSub(
				\DB::table('orders')
					->select([
						'id',
						\DB::raw('row_number() over (partition by producer_id order by id asc) as producer_order_id')
					])
					->where('producer_id', $this->producer->id),
				'producer_order_ids',
				fn(JoinClause $join) =>
					$join->on('orders.id', '=', 'producer_order_ids.id')
			)
			->where('producer_id', $this->producer->id)
			->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
			->with([
				'user',
				'assignee',
				'products',
				'transaction',
			])
			->orderBy('created_at', 'desc')
			->get();
	}

	public function processOrder($orderData)
	{
		// todo ...
	}
}
