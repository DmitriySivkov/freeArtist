<?php

namespace App\Enums;

class PaymentProviderEnum
{
	const YOOKASSA = 1;
	const ROBOKASSA = 2;

	const SECRETS = [
		self::YOOKASSA => 'secret_key',
		self::ROBOKASSA => 'secret_key'
	];

	public static function getPaymentProviderSecretKey($provider): string
	{
		if (!\Arr::has(self::SECRETS, $provider)) {
			return false;
		}

		return self::SECRETS[$provider];
	}
}
