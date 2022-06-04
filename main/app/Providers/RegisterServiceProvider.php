<?php

namespace App\Providers;

use App\Contracts\Requests\RegisterRequestContract;
use App\Contracts\Services\RegisterServiceContract;
use App\Http\Requests\Register\CustomerRegisterRequest;
use App\Http\Requests\Register\ProducerRegisterRequest;
use App\Services\Register\CustomerRegisterService;
use App\Services\Register\ProducerRegisterService;
use Illuminate\Support\ServiceProvider;

class RegisterServiceProvider extends ServiceProvider
{
	const CASE_PRODUCER_REGISTER = 1;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RegisterRequestContract::class, function () {
			switch (request()->get('case')) {
				case self::CASE_PRODUCER_REGISTER:
					return new ProducerRegisterRequest();
				default:
					return new CustomerRegisterRequest();
			}

		});

		$this->app->bind(RegisterServiceContract::class, function () {
			switch (request()->get('case')) {
				case self::CASE_PRODUCER_REGISTER:
					return new ProducerRegisterService();
				default:
					return new CustomerRegisterService();
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
