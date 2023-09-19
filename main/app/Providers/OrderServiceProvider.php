<?php

namespace App\Providers;

use App\Contracts\OrderServiceContract;
use App\Models\Role;
use App\Models\User;
use App\Services\Orders\ProducerOrderService;
use App\Services\Orders\UserOrderService;
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
		$this->app->bind(OrderServiceContract::class, function () {
			/** @var User $user */
			$user = auth('sanctum')->user();
			switch ($user->role_id) {
				case Role::ROLE_PRODUCER:
					return new ProducerOrderService();
				default:
					return new UserOrderService();
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
