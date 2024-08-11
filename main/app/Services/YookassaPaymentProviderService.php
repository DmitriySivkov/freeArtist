<?php

namespace App\Services;

use App\Contracts\YookassaClientServiceContract;
use App\Enums\PaymentProviderEnum;
use App\Enums\TransactionEnum;
use App\Models\PaymentMethod;
use App\Models\ProducerPaymentProvider;
use App\Models\Product;
use Illuminate\Support\Facades\Crypt;

class YookassaPaymentProviderService extends PaymentProviderService
{
	public function makeTransaction(): array
	{
		$productNames = Product::whereIn('id', collect($this->requestProducts)->pluck('id'))
			->pluck('title', 'id');

		$paymentProvider = ProducerPaymentProvider::where('producer_id', $this->producerId)
			->where('payment_provider_id', PaymentProviderEnum::YOOKASSA)
			->firstOrFail();

		/** @var YookassaClientService $service */
		$service = app(YookassaClientServiceContract::class);

		$service->setShopId($paymentProvider->payment_provider_data['shop_id']);
		$service->setSecretKey(
			Crypt::decryptString($paymentProvider->payment_provider_data['secret_key'])
		);

		$client = $service->getClient();

		/** @var TransactionService $transactionService */
		$transactionService = app(TransactionService::class);

		$transaction = $transactionService->createTransaction(
			$this->producerId,
			$this->requestProducts,
			PaymentMethod::PAYMENT_METHOD_CARD_ID,
			PaymentProviderEnum::YOOKASSA,
			TransactionEnum::TRANSACTION_STATUS_PROCESS
		);

		$builder = \YooKassa\Request\Payments\CreatePaymentRequest::builder();

		$totalPrice = collect($this->requestProducts)->sum(fn($item) => $item['price'] * $item['amount']);

		$builder->setAmount($totalPrice)
			->setCurrency(\YooKassa\Model\CurrencyCode::RUB)
			->setCapture(true)
			->setMetadata([
				'transaction_uuid'	=> $transaction->uuid, // todo - is needed?
			]);

		$builder->setConfirmation([
			'type'	=> \YooKassa\Model\Payment\ConfirmationType::EMBEDDED
		]);

		$builder->setReceiptEmail(config('yookassa.test_email')); // todo email
		$builder->setReceiptPhone(config('yookassa.test_phone')); // todo phone

		foreach ($this->requestProducts as $product) {
			$product['title'] = $productNames[$product['id']];

			// todo vat
			$builder->addReceiptItem(
				$product['title'],
				$product['price'],
				$product['amount'],
				2,
			);
		}

		$request = $builder->build();

		$request->setDescription('Операция из приложения FreeArtist');

		$idempotenceKey = uniqid('', true);

		$response = $client->createPayment($request, $idempotenceKey);

		$transaction->update([
			'status' => TransactionEnum::YOOKASSA_STATUSES[$response->status] ?? 0 // '0' status === 'unknown'
		]);

		if (
			$response->status === TransactionEnum::YOOKASSA_STATUS_ID_TO_NAME[TransactionEnum::TRANSACTION_STATUS_CANCEL]
			|| \Arr::exists($response->toArray(), 'error')
		) {
			\Log::info(
				'Не удалось принять платеж, ответ провайдера: ' .
				PHP_EOL .
				print_r($response->toArray(),true)
			);

			throw new \LogicException('Не удалось выполнить платеж');
		}

		return [
			'confirmation'			=> $response->getConfirmation(),
			'transaction_uuid'		=> $transaction->uuid,
		];
	}
}
