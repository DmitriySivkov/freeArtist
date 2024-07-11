<?php

namespace App\Services;


use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionService
{
	/**
	 * @param int $producerId
	 * @param array $orderData
	 * @param int $paymentMethod
	 * @param int $paymentProviderId
	 * @param int $status
	 * @return Transaction
	 */
	public function createTransaction(
		int $producerId, array $orderData, int $paymentMethod, int $paymentProviderId, int $status
	): Transaction
	{
		return Transaction::create([
			'uuid'					=> Str::uuid()->toString(),
			'producer_id'			=> $producerId,
			'order_data'			=> $orderData,
			'payment_method'		=> $paymentMethod,
			'payment_provider_id'	=> $paymentProviderId,
			'status'				=> $status
		]);
	}
}
