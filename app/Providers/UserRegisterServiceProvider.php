<?php

namespace App\Providers;

use App\Contracts\Requests\UserRegisterRequestContract;
use App\Contracts\Services\UserRegisterServiceContract;
use App\Http\Requests\Register\ProducerRegisterRequest;
use App\Http\Requests\Register\CustomerRegisterRequest;
use App\Models\Role;
use App\Services\Register\ProducerRegisterService;
use App\Services\Register\CustomerRegisterService;
use Illuminate\Support\ServiceProvider;

class UserRegisterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRegisterRequestContract::class, function () {
        	switch (request()->get('role_id')) {
				case Role::CUSTOMER:
					return new CustomerRegisterRequest();
				case Role::PRODUCER:
					return new ProducerRegisterRequest();
			}
			return false;
		});

		$this->app->bind(UserRegisterServiceContract::class, function () {
			switch (request()->get('role_id')) {
				case Role::CUSTOMER:
					return new CustomerRegisterService();
				case Role::PRODUCER:
					return new ProducerRegisterService();
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
