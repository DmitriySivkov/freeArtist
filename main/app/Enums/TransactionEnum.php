<?php

namespace App\Enums;

class TransactionEnum
{
	const TRANSACTION_STATUS_NEW 		= 1;
	const TRANSACTION_STATUS_SUCCESS 	= 2;
	const TRANSACTION_STATUS_CANCEL 	= 3;

	const TRANSACTION_STATUSES = [
		self::TRANSACTION_STATUS_NEW 		=> 'Новая',
		self::TRANSACTION_STATUS_SUCCESS	=> 'Успешно',
		self::TRANSACTION_STATUS_CANCEL		=> 'Отмена'
	];

	const YOOKASSA_TRANSACTION_STATUSES = [
		'payment.succeeded'	=> self::TRANSACTION_STATUS_SUCCESS,
		'payment.canceled'	=> self::TRANSACTION_STATUS_CANCEL,
	];
}
