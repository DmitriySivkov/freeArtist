<?php

namespace App\Providers;

use App\Contracts\YookassaClientServiceContract;
use App\Services\YookassaClientService;
use Illuminate\Support\ServiceProvider;

class YookassaClientServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(YookassaClientServiceContract::class, function() {
			return new YookassaClientService();
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
