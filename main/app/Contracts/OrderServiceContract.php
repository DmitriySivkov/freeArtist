<?php


namespace App\Contracts;


interface OrderServiceContract
{
	/**
	 * @param array $orderData
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function processOrder($orderData);
}
