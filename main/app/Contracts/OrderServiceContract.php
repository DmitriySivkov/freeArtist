<?php


namespace App\Contracts;


use App\Models\Transaction;

interface OrderServiceContract
{
	/**
	 * @param Transaction $transaction
	 * @return \Illuminate\Database\Eloquent\Model
	 */
	public function processOrder(Transaction $transaction);
}
