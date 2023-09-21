<?php


namespace App\Services\Orders;


use App\Contracts\OrderServiceContract;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class UserOrderService implements OrderServiceContract
{
	/**
	 * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getOrderList()
	{
		/** @var User $user */
		$user = auth('sanctum')->user();

		$query = Order::where('user_id', $user->id)
			->orderBy('id', 'desc')
			->withCasts([
				'created_at' => 'datetime:d-m-Y H:i:s',
				'updated_at' => 'datetime:d-m-Y H:i:s'
			]);

		if (request()->has('filter')) {

			$filter = json_decode(request()->input('filter'), true);

			if (Arr::has($filter, 'date')) {
				$filter['date'] = Carbon::parse($filter['date'])->format('Y-m-d');
				$query->whereDate('created_at', $filter['date']);
			}
		}

		$orders = $query->with([
			'producer.team',
			'products.images',
			'products.thumbnail'
		])->get();

		return $orders;
	}

	public function processOrder($orderData)
	{
		info(print_r($orderData,true));
//		foreach ($orderData['cart'] as $cartItem)
//		{
//			Order::create([
//				'user_id' => $orderData['user_id'],
//				'producer_id' => $cartItem['producer']['id'],
//				'products' => collect($cartItem['product_list'])->map(function($product) {
//					return [
//						'product_id' => $product['data']['id'],
//						'amount' => $product['amount']
//					];
//				}),
//				'payment_method' => $orderData['payment_method'],
//				'status' => $orderData['status']
//			]);
//		}
	}
}
