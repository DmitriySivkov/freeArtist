<?php

namespace App\Contracts;

use App\Models\Transaction;

interface PaymentProviderContract
{
	public function makeTransaction(): array;

	public function setProducerId(int $producerId): PaymentProviderContract;

	public function setRequestProducts(array $requestProducts): PaymentProviderContract;
}
