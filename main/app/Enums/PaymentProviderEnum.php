<?php

namespace App\Enums;

use App\Services\CashPaymentProviderService;
use App\Services\YookassaPaymentProviderService;

class PaymentProviderEnum
{
	const YOOKASSA = 1;
	const ROBOKASSA = 2;
	const CASH = 3;
	const TBANK = 4;

	const PAYMENT_PROVIDER_SERVICE = [
		self::YOOKASSA	=> YookassaPaymentProviderService::class,
		self::CASH		=> CashPaymentProviderService::class
	];

	const SECRETS = [
		self::YOOKASSA => 'secret_key',
		self::ROBOKASSA => 'secret_key'
	];

	/**
	 * @param int $providerId
	 * @return bool|string
	 */
	public static function getPaymentProviderSecretKey(int $providerId): bool|string
	{
		if (!\Arr::has(self::SECRETS, $providerId)) {
			return false;
		}

		return self::SECRETS[$providerId];
	}

	/**
	 * @param int $providerId
	 * @return bool|string
	 */
	public static function getPaymentProviderService(int $providerId): bool|string
	{
		if (!\Arr::has(self::PAYMENT_PROVIDER_SERVICE, $providerId)) {
			return false;
		}

		return self::PAYMENT_PROVIDER_SERVICE[$providerId];
	}
}
