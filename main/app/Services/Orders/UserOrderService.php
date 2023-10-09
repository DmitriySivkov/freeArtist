<?php


namespace App\Services\Orders;


use App\Contracts\OrderServiceContract;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Symfony\Component\Mime\Exception\LogicException;

class UserOrderService implements OrderServiceContract
{
	/**
	 * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getOrderList()
	{
		/** @var User $user */
		$user = auth()->user();

		$query = Order::query()
			->where('user_id', $user->id)
			->orderBy('id', 'desc')
			->withCasts([
				'created_at' => 'datetime:d-m-Y H:i',
				'updated_at' => 'datetime:d-m-Y H:i'
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

		$orders->each(function(Order $order) {
			$order->setAttribute(
				'order_products',
				array_combine(
					collect($order->order_products)->pluck('product_id')->toArray(),
					$order->order_products
				)
			);
		});

		return $orders;
	}

	public function processOrder($orderData)
	{
		Order::create([
			'user_id' => $orderData['user_id'],
			'producer_id' => $orderData['producer_id'],
			'payment_method' => $orderData['payment_method'],
			'status' => $orderData['status'],
			'order_products' => $orderData['order_products']
		]);

		$this->decreaseProducts($orderData['order_products']);
	}

	public function findInvalidProducts(array $orderProducts)
	{
		$orderProducts = collect($orderProducts)->pluck('amount', 'product_id');

		$products = Product::whereIn('id', $orderProducts->keys())
			->get();

		return $products->filter(fn(Product $product) =>
			$product->amount < $orderProducts[$product->id] || !$product->is_active
		);
	}

	private function decreaseProducts($orderProducts)
	{
		$orderProductAmounts = collect($orderProducts)->pluck('amount', 'product_id');

		$products = Product::whereIn('id', $orderProductAmounts->keys())->get();

		$products->each(function(Product $product) use ($orderProductAmounts) {
			$product->amount = $product->amount - $orderProductAmounts[$product->id];

			if ($product->amount < 0) {
				throw new LogicException('Некорректное количество товара');
			}

			$product->save();
		});
	}
}
