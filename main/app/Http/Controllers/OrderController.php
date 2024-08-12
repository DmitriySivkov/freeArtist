<?php

namespace App\Http\Controllers;

use App\Helpers\TokenHelper;
use App\Http\Requests\UserNewOrderRequest;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\ProducerOrderPriority;
use App\Models\Transaction;
use App\Models\User;
use App\Services\UserOrderService;
use App\Services\PaymentProviderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
	public function index(UserOrderService $orderService)
    {
		/** @var User|null $user */
		$user = TokenHelper::getUserByToken(request()->cookie('token'));

		$orderService->setUser($user);

        return $orderService->getOrderList();
    }

    public function store(Request $request, UserOrderService $orderService)
	{
		$token = request()->cookie('token');

		/** @var User|null $user */
		$user = TokenHelper::getUserByToken($token);
		$meta = $request->input('meta');
		$transactionUuid = $request->input('transaction_uuid');

		$transaction = Transaction::whereUuid($transactionUuid)
			->firstOrFail();

		try {
			\DB::beginTransaction();

			$orderService->setUser($user);
			$orderService->setMeta($meta);

			$order = $orderService->processOrder($transaction);

			// todo - move to service
			$orderPriority = ProducerOrderPriority::where('producer_id', $order->producer_id)
				->where('status', Order::ORDER_STATUS_NEW)
				->first();

			if ($orderPriority) {
				$orderPriority->order_priority = [$order->id, ...$orderPriority->order_priority];
				$orderPriority->save();
			} else {
				ProducerOrderPriority::create([
					'producer_id' => $order->producer_id,
					'status' => Order::ORDER_STATUS_NEW,
					'order_priority' => [$order->id]
				]);
			}

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
		$paymentMethod		= $request->input('payment_method');
		$producerId			= $request->input('producer_id');

		$requestProducts	= array_combine(
			collect($request->input('products'))
				->pluck('id')
				->toArray(),
			$request->input('products')
		);

		try {
			\DB::beginTransaction();

			$transactionData = PaymentProviderService::getPaymentProvider($paymentMethod)
				->setProducerId($producerId)
				->setRequestProducts(array_values($requestProducts))
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
