<?php


namespace App\Services\Orders;


use App\Contracts\OrderServiceContract;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Exception\LogicException;

class UserOrderService implements OrderServiceContract
{
	private ?User $user = null;

	/**
	 * @param User|null $user
	 * @return void
	 */
	public function setUser(?User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getOrderList()
	{
		$orderUuid = json_decode(request()->cookie('orders'));

		$query = Order::query()
			->when(
				$this->user,
				fn($query) => $query->where('user_id', $this->user->id),
				fn($query) => $query->whereIn('uuid', $orderUuid ?? []),
			)
			->orderBy('id', 'desc')
			->withCasts([
				'created_at' => 'datetime:d-m-Y H:i',
				'updated_at' => 'datetime:d-m-Y H:i'
			]);

		$orders = $query->with([
			'producer.team',
			'products.images',
			'products.thumbnail'
		])->get();

		$orders->each(function(Order $order) {
			$order->setAttribute(
				'order_products',
				array_combine(
					collect($order->order_products)
						->pluck('product_id')
						->toArray(),
					$order->order_products
				)
			);
		});

		return $orders;
	}

	/**
	 * @param $orderData
	 * @return Order|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
	 */
	public function processOrder($orderData)
	{
		$order = Order::create([
			'uuid' => (string)Str::uuid(),
			'user_id' => $this->user ? $this->user->id : null,
			'producer_id' => $orderData['producer_id'],
			'payment_method' => $orderData['payment_method'],
			'status' => Order::ORDER_STATUS_NEW,
			'order_products' => $orderData['order_products'],
			'order_meta' => $orderData['meta']
		]);

		$this->decreaseProducts($orderData['order_products']);

		return $order;
	}

	/**
	 * @param array $orderProducts
	 * @return \Illuminate\Support\Collection
	 */
	public function findInvalidProducts(array $orderProducts)
	{
		$orderProducts = collect($orderProducts)->pluck('amount', 'product_id');

		$products = Product::whereIn('id', $orderProducts->keys())
			->get();

		return $products->filter(fn(Product $product) =>
			$product->amount < $orderProducts[$product->id] || !$product->is_active
		);
	}

	/**
	 * @param $orderProducts
	 * @return void
	 */
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
