<?php

namespace App\Providers;

use App\Contracts\Requests\NewOrderRequestContract;
use App\Contracts\Services\OrderServiceContract;
use App\Http\Requests\Order\CustomerNewOrderRequest;
use App\Http\Requests\Order\ProducerNewOrderRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\Orders\ProducerOrderService;
use App\Services\Orders\CustomerOrderService;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->bind(NewOrderRequestContract::class, function () {
			/** @var User $user */
			$user = auth('sanctum')->user();
			switch ($user->role_id) {
				case Role::PRODUCER:
					return new ProducerNewOrderRequest();
				default:
					return new CustomerNewOrderRequest();
			}
		});

		$this->app->bind(OrderServiceContract::class, function () {
			/** @var User $user */
			$user = auth('sanctum')->user();
			switch ($user->role_id) {
				case Role::PRODUCER:
					return new ProducerOrderService();
				default:
					return new CustomerOrderService();
			}
		});
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
