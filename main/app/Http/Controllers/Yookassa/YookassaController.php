<?php

namespace App\Http\Controllers\Yookassa;

use App\Contracts\YookassaClientServiceContract;
use App\Enums\PaymentProviderEnum;
use App\Http\Controllers\Controller;
use App\Models\ProducerPaymentProvider;
use App\Services\YookassaClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class YookassaController extends Controller
{
	public function createPayment(Request $request)
	{
		$producerId = $request->input('producer_id');
		$price = $request->input('price');

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
				->setCapture(true);
//				->setMetadata([
//					'cms_name'       => 'yoo_api_test',
//					'order_id'       => '112233',
//					'language'       => 'ru',
//					'transaction_id' => '123-456-789',
//				]);

			// Устанавливаем способ подтверждения
			$builder->setConfirmation([
				'type'      => \YooKassa\Model\Payment\ConfirmationType::EMBEDDED
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
//			$builder->addReceiptShipping(
//				'Delivery/Shipping/Доставка',
//				100,
//				1,
//				\YooKassa\Model\Receipt\PaymentMode::FULL_PAYMENT,
//				\YooKassa\Model\Receipt\PaymentSubject::SERVICE
//			);

			// Создаем объект запроса
			$request = $builder->build();

			// Можно изменить данные, если нужно
			$request->setDescription($request->getDescription() . ' - merchant comment');

			$idempotenceKey = uniqid('', true);
			$response = $client->createPayment($request, $idempotenceKey);

			//получаем confirmation
			return $response->getConfirmation();
		} catch (\Exception $e) {
			return response($e->getMessage(), 422);
		}
	}

    public function status()
	{
		info(print_r(request()->all(),true));
//		/** @var YookassaClientService $service */
//		$service = app(YookassaClientServiceContract::class);
//
//		$service->setShopId();
//		$service->setSecretKey();
//
//		$client = $service->getClient();
	}
}
