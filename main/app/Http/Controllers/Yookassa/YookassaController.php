<?php

namespace App\Http\Controllers\Yookassa;

use App\Contracts\YookassaClientServiceContract;
use App\Enums\PaymentProviderEnum;
use App\Enums\TransactionEnum;
use App\Http\Controllers\Controller;
use App\Models\ProducerPaymentProvider;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\YookassaClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class YookassaController extends Controller
{
	public function create(Request $request)
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

			$transactionUuid = Str::uuid()->toString();

			$builder->setAmount($totalPrice)
				->setCurrency(\YooKassa\Model\CurrencyCode::RUB)
				->setCapture(true)
				->setMetadata([
					'transaction_uuid'	=> $transactionUuid,
				]);

			$builder->setConfirmation([
				'type'	=> \YooKassa\Model\Payment\ConfirmationType::EMBEDDED
			]);

			$builder->setReceiptEmail(config('yookassa.test_email')); // todo email
			$builder->setReceiptPhone(config('yookassa.test_phone')); // todo phone

			$products = Product::whereIn('id', array_keys($requestProducts))
				->get();

			/** @var Product $product */
			foreach ($products as $product) {
				// todo vat
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

			Transaction::create([
				'uuid'					=> $transactionUuid,
				'order_id'				=> null,
				'payment_provider_id'	=> PaymentProviderEnum::YOOKASSA,
				'status'				=> TransactionEnum::TRANSACTION_STATUS_NEW
			]);

			return $response->getConfirmation();
		} catch (\Exception $e) {
			return response($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
		}
	}

    public function status(Request $request)
	{
		$event	= $request->input('event');
		$data	= $request->input('object');

		$transaction = Transaction::where('uuid', $data['metadata']['transaction_uuid'])
			->first();

		if (!$transaction) {
			return ;
		}

		$transaction->update([
			'status' => TransactionEnum::YOOKASSA_TRANSACTION_STATUSES[$event]
		]);
	}
}
