<?php

namespace App\Providers;

use App\Contracts\Services\OrderServiceContract;
use App\Models\Role;
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
		$this->app->bind(OrderServiceContract::class, function () {
			$filter = json_decode(request()->get('filter'), true);
			switch ($filter['user']['role_id']) {
				case Role::CUSTOMER:
					return new CustomerOrderService();
				case Role::PRODUCER:
					return new ProducerOrderService();
			}
			return false;
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
