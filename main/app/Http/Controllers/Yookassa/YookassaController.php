<?php

namespace App\Http\Controllers\Yookassa;

use App\Contracts\YookassaClientServiceContract;
use App\Enums\PaymentProviderEnum;
use App\Http\Controllers\Controller;
use App\Models\ProducerPaymentProvider;
use App\Models\Product;
use App\Services\YookassaClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class YookassaController extends Controller
{
	public function createPayment(Request $request)
	{
		$producerId 		= $request->input('producer_id');
		$totalPrice 		= $request->input('price');
		$requestProducts 	= array_combine(
			collect($request->input('products'))
				->pluck('product_id')
				->toArray(),
			$request->input('products')
		);

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
			$builder->setAmount($totalPrice)
				->setCurrency(\YooKassa\Model\CurrencyCode::RUB)
				->setCapture(true)
				->setMetadata([
					'producer_id'		=> $producerId,
					'transaction_id'	=> Str::uuid()->toString(),
				]);

			$builder->setConfirmation([
				'type'	=> \YooKassa\Model\Payment\ConfirmationType::EMBEDDED
			]);

			$builder->setReceiptEmail(config('yookassa.test_email'));
			$builder->setReceiptPhone(config('yookassa.test_phone'));

			$products = Product::whereIn('id', array_keys($requestProducts))
				->get();

			/** @var Product $product */
			foreach ($products as $product) {
				// todo - request vat from user
				$builder->addReceiptItem(
					$product->title,
					$product->price,
					$requestProducts[$product->id]['amount'],
					2,
				);
			}

			$request = $builder->build();

			$request->setDescription('Операция из приложения FreeArtist');

			$idempotenceKey = uniqid('', true);
			$response = $client->createPayment($request, $idempotenceKey);

			return $response->getConfirmation();
		} catch (\Exception $e) {
			return response($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
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
