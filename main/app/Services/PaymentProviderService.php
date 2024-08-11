<?php

namespace App\Services;

use App\Contracts\PaymentProviderContract;
use App\Enums\PaymentProviderEnum;
use App\Models\Transaction;

abstract class PaymentProviderService implements PaymentProviderContract
{
	protected int $producerId;
	protected array $requestProducts;
	protected Transaction $transaction;

	abstract public function makeTransaction(): array;

	public static function getPaymentProvider(int $paymentMethod): PaymentProviderContract
	{
		return app(PaymentProviderEnum::getPaymentProviderService($paymentMethod));
	}

	public function setProducerId(int $producerId): PaymentProviderContract
	{
		$this->producerId = $producerId;

		return $this;
	}

	public function setRequestProducts(array $requestProducts): PaymentProviderContract
	{
		$this->requestProducts = $requestProducts;

		return $this;
	}
}
