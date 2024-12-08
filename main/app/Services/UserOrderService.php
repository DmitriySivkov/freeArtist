<?php


namespace App\Services;


use App\Contracts\OrderServiceContract;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Symfony\Component\Mime\Exception\LogicException;

class UserOrderService implements OrderServiceContract
{
	private ?User $user;
	private Carbon $prepareByDate;
	private array $unauthTransactionUuids = [];

	/**
	 * @param User|null $user
	 * @return $this
	 */
	public function setUser(?User $user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @param Carbon $date
	 * @return $this
	 */
	public function setPreparationDate(Carbon $date)
	{
		$this->prepareByDate = $date;

		return $this;
	}

	/**
	 * @param array $transactionUuids
	 * @return $this
	 */
	public function setUnauthTransactionUuids(array $transactionUuids) {
		$this->unauthTransactionUuids = $transactionUuids;

		return $this;
	}

	/**
	 * @return Order[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
	 */
	public function getOrderList()
	{
		$query = Order::query()
			->when(
				$this->user,
				fn($query) => $query->where('user_id', $this->user->id),
				fn($query) => $query->whereHas('transaction', fn($q) => $q->whereIn('uuid', $this->unauthTransactionUuids)),
			)
			->orderBy('id', 'desc')
			->withCasts([
				'created_at' => 'datetime:d-m-Y H:i',
				'updated_at' => 'datetime:d-m-Y H:i'
			]);

		$orders = $query->with([
			'producer.team',
			'products.images',
			'products.thumbnail',
			'transaction:id,uuid,status'
		])->get();

		$orders->map(function(Order $order) {
			$order->order_products = array_combine(
				collect($order->order_products)
					->pluck('id')
					->toArray(),
				$order->order_products
			);

			return $order;
		});

		return $orders;
	}

	/**
	 * @param Transaction $transaction
	 * @return Order|\Illuminate\Database\Eloquent\Model
	 */
	public function processOrder(Transaction $transaction)
	{
		$producerOrderCount = Order::where('producer_id', $transaction->producer_id)
			->count();

		$order = Order::create([
			'transaction_id'	=> $transaction->id,
			'user_id'			=> $this->user ? $this->user->id : null,
			'producer_id'		=> $transaction->producer_id,
			'producer_order_id'	=> $producerOrderCount ? $producerOrderCount + 1 : 1, // if there were no orders yet - assign 1
			'order_products'	=> $transaction->order_data,
			'status'			=> Order::ORDER_STATUS_NEW,
			'prepare_by'		=> $this->prepareByDate,
			'assignee_id'		=> $transaction->producer->team->user_id // owner
		]);

		$this->decreaseProducts($transaction->order_data);

		return $order;
	}

	/**
	 * @param array $orderProducts
	 * @return \Illuminate\Support\Collection
	 */
	public function findInvalidProducts(array $orderProducts)
	{
		$orderProducts = collect(
			array_combine(
				collect($orderProducts)
					->pluck('id')
					->toArray(),
				$orderProducts
			)
		);

		$products = Product::whereIn('id', $orderProducts->keys())
			->get();

		return $products->filter(function(Product $product) use ($orderProducts) {
			return $product->amount < $orderProducts[$product->id]['amount']
				|| !$product->is_active
				|| $product->price !== (float)$orderProducts[$product->id]['price'];
			})
			->map(function(Product $product) use ($orderProducts) {
				$product->cart_amount = $orderProducts[$product->id]['amount'];
				$product->cart_price = $orderProducts[$product->id]['price'];

				return $product;
			});
	}

	/**
	 * @param $orderProducts
	 * @return void
	 */
	private function decreaseProducts($orderProducts)
	{
		$orderProductAmounts = collect($orderProducts)->pluck('amount', 'id');

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
