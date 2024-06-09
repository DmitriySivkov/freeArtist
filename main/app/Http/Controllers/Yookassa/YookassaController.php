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
		$paymentMethod 		= $request->input('payment_method');
		$totalPrice			= $request->input('price');
		$requestProducts 	= array_combine(
			collect($request->input('products'))
				->pluck('id')
				->toArray(),
			$request->input('products')
		);

		$productNames = Product::whereIn('id', array_keys($requestProducts))
			->pluck('title', 'id');

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

			foreach ($requestProducts as $productId => $product) {
				$product['title'] = $productNames[$productId];

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

			Transaction::create([
				'uuid'					=> $transactionUuid,
				'producer_id'			=> $producerId,
				'order_data'			=> array_values($requestProducts),
				'payment_method'		=> $paymentMethod,
				'payment_provider_id'	=> PaymentProviderEnum::YOOKASSA,
				'status'				=> TransactionEnum::YOOKASSA_STATUSES[$response->status] ?? 0, // '0' status === 'unknown'
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
				'confirmation' 		=> $response->getConfirmation(),
				'transaction_uuid'	=> $transactionUuid,
			];
		} catch (\Exception $e) {
			return response($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
		}
	}

    public function status(Request $request)
	{
		$data = $request->input('object');

		$transaction = Transaction::where('uuid', $data['metadata']['transaction_uuid'])
			->first();

		if (!$transaction) {
			\Log::error("Could not find transaction with uuid: {$data['metadata']['transaction_uuid']}");

			return ;
		}

		$transaction->update([
			'status' => TransactionEnum::YOOKASSA_STATUSES[$data['status']]
		]);
	}
}
