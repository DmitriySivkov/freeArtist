<?php

namespace App\Services;

use App\Enums\PaymentProviderEnum;
use App\Enums\TransactionEnum;
use App\Models\PaymentMethod;

class CashPaymentProviderService extends PaymentProviderService
{
	public function makeTransaction(): array
	{
		/** @var TransactionService $transactionService */
		$transactionService = app(TransactionService::class);

		if (!$this->phone) {
			throw new \LogicException('Не указан телефон');
		}

		$transaction = $transactionService->createTransaction(
			$this->producerId,
			$this->requestProducts,
			$this->phone,
			PaymentMethod::PAYMENT_METHOD_CASH_ID,
			PaymentProviderEnum::CASH,
			TransactionEnum::TRANSACTION_STATUS_PROCESS
		);

		return [
			'transaction_uuid' => $transaction->uuid
		];
	}
}
