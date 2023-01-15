<?php


namespace App\Contracts;


interface OrderServiceContract
{
	/**
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function getList();

	/**
	 * @param array $orderData
	 * @return \Illuminate\Database\Eloquent\Collection
	 */
	public function processOrder($orderData);
}
