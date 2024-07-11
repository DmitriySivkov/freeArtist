<?php

namespace App\Services;

use App\Enums\PaymentProviderEnum;
use App\Enums\TransactionEnum;
use App\Models\PaymentMethod;

class CashPaymentProviderService extends PaymentProviderService
{
	public function makePayment()
	{
		/** @var TransactionService $transactionService */
		$transactionService = app(TransactionService::class);

		$transaction = $transactionService->createTransaction(
			$this->producerId,
			$this->requestProducts,
			PaymentMethod::PAYMENT_METHOD_CASH_ID,
			PaymentProviderEnum::CASH,
			TransactionEnum::TRANSACTION_STATUS_PROCESS
		);

		$this->setTransaction($transaction);
	}
}
