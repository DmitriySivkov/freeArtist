<?php

namespace App\Contracts;

use App\Models\Transaction;

interface PaymentProviderContract
{
	public function makePayment();

	public function setProducerId(int $producerId): PaymentProviderContract;

	public function setRequestProducts(array $requestProducts): PaymentProviderContract;

	public function setTransaction(Transaction $transaction): PaymentProviderContract;

	public function getTransaction(): Transaction;
}
