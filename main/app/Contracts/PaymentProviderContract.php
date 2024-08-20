<?php

namespace App\Contracts;

interface PaymentProviderContract
{
	public function makeTransaction(): array;

	public function setProducerId(int $producerId): PaymentProviderContract;

	public function setRequestProducts(array $requestProducts): PaymentProviderContract;

	public function setPhone(string $phone): PaymentProviderContract;
}
