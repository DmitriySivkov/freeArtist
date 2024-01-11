<?php

namespace App\Console\Commands;

use App\Contracts\YookassaClientServiceContract;
use App\Enums\PaymentProviderEnum;
use App\Models\ProducerPaymentProvider;
use App\Services\YookassaClientService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class TestCommand extends Command
{
    protected $signature = 'test:go';

    public function handle()
    {
		$producerId = 28;

		if (app()->environment() === 'production') {
			$producerId = 26;
		}

		$price = 250;

		$paymentProvider = ProducerPaymentProvider::where('producer_id', $producerId)
			->where('payment_provider_id', PaymentProviderEnum::YOOKASSA)
			->firstOrFail();

		/** @var YookassaClientService $service */
		$service = app(YookassaClientServiceContract::class);

		$service->setShopId($paymentProvider->payment_provider_data['shop_id']);
		$service->setSecretKey(
			Crypt::decryptString($paymentProvider->payment_provider_data['secret_key'])
		);

		$client = $service->getClient();

		try {
			$builder = \YooKassa\Request\Payments\CreatePaymentRequest::builder();
			$builder->setAmount($price)
				->setCurrency(\YooKassa\Model\CurrencyCode::RUB)
				->setCapture(true)
				->setMetadata([
					'cms_name'       => 'yoo_api_test',
					'order_id'       => '112233',
					'language'       => 'ru',
					'transaction_id' => '123-456-789',
				]);

			// Устанавливаем страницу для редиректа после оплаты
			$builder->setConfirmation([
				'type'      => \YooKassa\Model\Payment\ConfirmationType::REDIRECT,
				'returnUrl' => 'https://c1ce-217-15-159-95.ngrok-free.app',
			]);

			// Составляем чек
			$builder->setReceiptEmail('john.doe@merchant.com');
			$builder->setReceiptPhone('71111111111');
			// Добавим товар
			$builder->addReceiptItem(
				'Платок Gucci',
				3000,
				1.0,
				2,
				'full_payment',
				'commodity'
			);
			// Добавим доставку
			$builder->addReceiptShipping(
				'Delivery/Shipping/Доставка',
				100,
				1,
				\YooKassa\Model\Receipt\PaymentMode::FULL_PAYMENT,
				\YooKassa\Model\Receipt\PaymentSubject::SERVICE
			);

			// Можно добавить распределение денег по магазинам
			$builder->setTransfers([
				[
					'account_id' => 123456,
					'amount' => [
						'value' => 1000,
						'currency' => \YooKassa\Model\CurrencyCode::RUB
					],
				],
				[
					'account_id' => 654321,
					'amount' => [
						'value' => 2000,
						'currency' => \YooKassa\Model\CurrencyCode::RUB
					],
				]
			]);

			// Создаем объект запроса
			$request = $builder->build();

			// Можно изменить данные, если нужно
			$request->setDescription($request->getDescription() . ' - merchant comment');

			$idempotenceKey = uniqid('', true);
			$response = $client->createPayment($request, $idempotenceKey);

			//получаем confirmationUrl для дальнейшего редиректа
			$confirmationUrl = $response->getConfirmation()->getConfirmationUrl();

			info(print_r($confirmationUrl,true));
		} catch (\Exception $e) {
			info($e->getMessage());
			$response = $e;
		}
    }
}
