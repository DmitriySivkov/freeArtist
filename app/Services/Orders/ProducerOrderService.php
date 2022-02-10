<?php


namespace App\Services\Orders;


use App\Contracts\Services\OrderServiceContract;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class ProducerOrderService implements OrderServiceContract
{
	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getList()
	{
		$query = Order::query();

		if (request()->has('filter')) {

			$filter = json_decode(request()->get('filter'), true);

			if (Arr::has($filter, 'date')) {
				$filter['date'] = Carbon::parse($filter['date'])->format('Y-m-d');
				$query->whereDate('created_at', $filter['date']);
			}

			if (Arr::has($filter, 'user.producer')) {
				$query->where('producer_id', $filter['user']['producer']['id']);
			}
		}

		return $query->get();
	}
}
