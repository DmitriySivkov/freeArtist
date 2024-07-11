<?php

namespace App\Enums;

use App\Services\CashPaymentProviderService;
use App\Services\YookassaPaymentProviderService;

class PaymentProviderEnum
{
	const YOOKASSA = 1;
	const ROBOKASSA = 2;
	const CASH = 3;

	const PAYMENT_PROVIDER_SERVICE = [
		self::YOOKASSA	=> YookassaPaymentProviderService::class,
		self::CASH		=> CashPaymentProviderService::class
	];

	const SECRETS = [
		self::YOOKASSA => 'secret_key',
		self::ROBOKASSA => 'secret_key'
	];

	public static function getPaymentProviderSecretKey($provider): bool|string
	{
		if (!\Arr::has(self::SECRETS, $provider)) {
			return false;
		}

		return self::SECRETS[$provider];
	}

	public static function getPaymentProviderService($provider): bool|string
	{
		if (!\Arr::has(self::PAYMENT_PROVIDER_SERVICE, $provider)) {
			return false;
		}

		return self::PAYMENT_PROVIDER_SERVICE[$provider];
	}
}
