<?php

namespace App\Enums;

class TransactionEnum
{
	const TRANSACTION_STATUS_SUCCESS 	= 1;
	const TRANSACTION_STATUS_PROCESS	= 2;
	const TRANSACTION_STATUS_CANCEL 	= 3;

	const TRANSACTION_STATUSES = [
		self::TRANSACTION_STATUS_SUCCESS	=> 'Успешно',
		self::TRANSACTION_STATUS_PROCESS	=> 'Обрабатывается',
		self::TRANSACTION_STATUS_CANCEL		=> 'Отмена'
	];

	const YOOKASSA_STATUSES = [
		'succeeded'	=> self::TRANSACTION_STATUS_SUCCESS,
		'pending'	=> self::TRANSACTION_STATUS_PROCESS,
		'canceled'	=> self::TRANSACTION_STATUS_CANCEL,
	];

	const YOOKASSA_STATUS_ID_TO_NAME = [
		self::TRANSACTION_STATUS_SUCCESS	=> 'succeeded',
		self::TRANSACTION_STATUS_PROCESS	=> 'pending',
		self::TRANSACTION_STATUS_CANCEL		=> 'canceled',
	];
}
