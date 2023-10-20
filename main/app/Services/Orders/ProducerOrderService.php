<?php


namespace App\Services\Orders;

use App\Contracts\OrderServiceContract;
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

	/**
	 * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getOrderList()
	{
		// todo - check access (middleware?)
		$date = request()->input('date') ?
			Carbon::parse(request()->input('date'))->format('Y-m-d') :
			Carbon::today();

		$query = Order::where('producer_id', $this->producer->id)
			->whereDate('created_at', $date)
			->orderBy('created_at', 'desc')
			->withCasts([
				'created_at' => 'datetime:d-m-Y H:i:s',
				'updated_at' => 'datetime:d-m-Y H:i:s'
			]);

		$orders = $query->with([
			'user',
			'products.images',
			'products.thumbnail'
		])->get();

		return $orders;
	}

	public function processOrder($orderData)
	{
		// todo ...
	}
}
