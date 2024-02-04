<?php

namespace App\Providers;

use App\Contracts\OrderServiceContract;
use App\Services\Orders\ProducerOrderService;
use App\Services\Orders\UserOrderService;
use Illuminate\Support\ServiceProvider;

class OrderServiceProvider extends ServiceProvider
{
	// todo - defer some providers ?

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->when(\App\Http\Controllers\OrderController::class)
			->needs(OrderServiceContract::class)
			->give(function () {
				return new UserOrderService();
			});

		$this->app->when(\App\Http\Controllers\Producer\OrderController::class)
			->needs(OrderServiceContract::class)
			->give(function () {
				return new ProducerOrderService();
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
