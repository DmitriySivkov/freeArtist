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
	public function getList()
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		$query = Order::query()->where('producer_id', $user->producer_id);;

		if (request()->has('filter')) {

			$filter = json_decode(request()->get('filter'), true);

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
