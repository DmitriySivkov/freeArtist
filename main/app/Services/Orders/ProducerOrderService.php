<?php


namespace App\Services\Orders;


use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class ProducerOrderService extends OrderService
{
	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getOrderList()
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		$query = Order::where('producer_id', $user->producer_id)
			->orderBy('id', 'desc');

		if (request()->has('filter')) {

			$filter = json_decode(request()->get('filter'), true);

			if (Arr::has($filter, 'date')) {
				$filter['date'] = Carbon::parse($filter['date'])->format('Y-m-d');
				$query->whereDate('created_at', $filter['date']);
			}
		}

		$orders = $query->get();

		return $orders;
	}
}
