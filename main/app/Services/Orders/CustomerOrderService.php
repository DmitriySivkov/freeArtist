<?php


namespace App\Services\Orders;


use App\Contracts\Services\OrderServiceContract;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class CustomerOrderService implements OrderServiceContract
{
	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getList()
	{
		$query = Order::query();

		if (request()->has('filter')) {

			$filter = json_decode(request()->get('filter'), true);

			$query->where('user_id', $filter['user']['id'])
				->withCasts([
					'created_at' => 'datetime:Y-m-d H:i:s',
					'updated_at' => 'datetime:Y-m-d H:i:s'
				]);

			if (Arr::has($filter, 'date')) {
				$filter['date'] = Carbon::parse($filter['date'])->format('Y-m-d');
				$query->whereDate('created_at', $filter['date']);
			}
		}

		$orders = $query->get();
		$orders->map(function(Order $order) {
			$order->created_at_parts = [
				'hi' => Carbon::parse($order->created_at)->format('H:i'),
				'date' => Carbon::parse($order->created_at)->format('d-m-Y')
			];
			$order->updated_at_parts = [
				'hi' => Carbon::parse($order->updated_at)->format('H:i'),
				'date' => Carbon::parse($order->updated_at)->format('d-m-Y')
			];
			return $order;
		});

		return $orders;
	}
}