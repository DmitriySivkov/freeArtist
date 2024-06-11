<?php

namespace App\Http\Controllers;

use App\Contracts\OrderServiceContract;
use App\Helpers\TokenHelper;
use App\Http\Requests\UserNewOrderRequest;
use App\Models\Order;
use App\Models\ProducerOrderPriority;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Orders\UserOrderService;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
	private OrderServiceContract $orderService;

	public function __construct(OrderServiceContract $orderService)
	{
		/** @var UserOrderService $orderService */
		$this->orderService = $orderService;
	}

	public function index(UserOrderService $orderService)
    {
		/** @var User|null $user */
		$user = TokenHelper::getUserByToken(request()->cookie('token'));

		$orderService->setUser($user);

        return $orderService->getOrderList();
    }

    public function store(UserNewOrderRequest $request)
	{
		/** @var User|null $user */
		$user = TokenHelper::getUserByToken(request()->cookie('token'));
		$meta = $request->input('meta');

		$transaction = Transaction::where('uuid', $request->input('transaction_uuid'))
			->firstOrFail();

		try {
			\DB::beginTransaction();

			$this->orderService->setUser($user);
			$this->orderService->setMeta($meta);

			$order = $this->orderService->processOrder($transaction);

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
			\DB::rollBack();

			\Log::error('Не удалось создать заказ, текст ошибки: ' . $e->getMessage());

			return response(
				['message' => 'Что-то пошло не так'],
				Response::HTTP_UNPROCESSABLE_ENTITY
			);
		}

		return response([
			'message' => 'Заказ принят',
			'uuid' => $order->transaction_uuid
		], 200);
	}


}
