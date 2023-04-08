<?php


namespace App\Services\Orders;


use App\Contracts\OrderServiceContract;
use App\Models\Order;

abstract class OrderService implements OrderServiceContract
{
	public function processOrder($orderData)
	{
		foreach ($orderData['cart'] as $cartItem)
		{
			Order::create([
				'user_id' => $orderData['user_id'],
				'producer_id' => $cartItem['producer']['id'],
				'products' => collect($cartItem['product_list'])->map(function($product) {
					return [
						'product_id' => $product['data']['id'],
						'amount' => $product['amount']
					];
				}),
				'payment_method' => $orderData['payment_method'],
				'status' => $orderData['status']
			]);
		}
	}

	abstract public function getOrderList();
}
