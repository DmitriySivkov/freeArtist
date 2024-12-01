<?php

namespace App\Http\Controllers;

use App\Helpers\TokenHelper;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\User;
use App\Services\UserOrderService;
use App\Services\PaymentProviderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
	public function index(Request $request, UserOrderService $orderService)
    {
		// todo - unify obtaining 'token' from cookie with capacitor secure-storage
		/** @var User|null $user */
		$user = TokenHelper::getUserByToken(request()->cookie('token'));

		$unauthTransactionUuids = $request->input('unauthTransactionUuids', []);

		$orderService
			->setUser($user)
			->setUnauthTransactionUuids($unauthTransactionUuids);

        return $orderService->getOrderList();
    }

    public function store(Request $request, UserOrderService $orderService)
	{
		$token = request()->cookie('token'); // todo - unify obtaining 'token' from cookie with capacitor secure-storage

		/** @var User|null $user */
		$user = TokenHelper::getUserByToken($token);

		$transactionUuid	= $request->input('transaction_uuid');
		$prepareBy			= Carbon::make($request->input('prepare_by'));

		$transaction = Transaction::whereUuid($transactionUuid)
			->firstOrFail();

		try {
			\DB::beginTransaction();

			$orderService
				->setUser($user)
				->setPreparationDate($prepareBy)
				->processOrder($transaction);

			\DB::commit();
		} catch (\Throwable $e) {
			// todo - если способ платежа онлайн - то к этому моменту оплата уже произведена.
			// todo - нужно как-то обрабатывать несозданные заказы по существующей транзакции
			\DB::rollBack();

			\Log::error(
				"Не удалось создать заказ, ID транзакции: {$transaction->id}" . PHP_EOL .
				'текст ошибки: ' . PHP_EOL .
				$e->getMessage()
			);

			// если оплата картой, то к этому моменту оплата уже произведена
			// даже в случае ошибки показываем юзеру сообщение об успехе
			// обрабатываем на бэке несозданный заказ отталкиваясь от транзакции
			if ($transaction->payment_method === PaymentMethod::PAYMENT_METHOD_CARD_ID) {
				return response([
					'message' => 'Заказ принят',
					'uuid' => $transaction->uuid
				], 200);
			}

			return response([
				'message' => 'Не удалось создать заказ',
				'uuid' => $transaction->uuid
			], Response::HTTP_UNPROCESSABLE_ENTITY);
		}

		return response([
			'message' => 'Заказ принят',
			'uuid' => $transaction->uuid
		], 200);
	}

	public function transaction(Request $request)
	{
		$token = request()->cookie('token');

		/** @var User|null $user */
		$user = TokenHelper::getUserByToken($token);

		$paymentMethod	= $request->input('payment_method');
		$producerId		= $request->input('producer_id');
		$phone			= $request->input('phone');

		$requestProducts	= array_combine(
			collect($request->input('products'))
				->pluck('id')
				->toArray(),
			$request->input('products')
		);

		if ($phone) {
			$phone = "8{$phone}";
		} else {
			$phone = $user->phone;
		}

		try {
			\DB::beginTransaction();

			$transactionData = PaymentProviderService::getPaymentProvider($paymentMethod)
				->setProducerId($producerId)
				->setRequestProducts(array_values($requestProducts))
				->setPhone($phone)
				->makeTransaction();

			\DB::commit();

			return $transactionData;
		} catch (\Throwable $e) {
			\DB::rollBack();

			\Log::error('Не удалось создать транзакцию, текст ошибки: ' . $e->getMessage());

			return response(
				['message' => 'Не удалось создать заказ'],
				Response::HTTP_UNPROCESSABLE_ENTITY
			);
		}
	}
}
