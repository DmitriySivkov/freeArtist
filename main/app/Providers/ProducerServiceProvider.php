<?php

namespace App\Providers;

use App\Contracts\ProducerServiceContract;
use App\Services\Producers\ProducerService;
use Illuminate\Support\ServiceProvider;

class ProducerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->singleton(ProducerServiceContract::class, function() {
			return new ProducerService();
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
