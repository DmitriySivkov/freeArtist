<?php

namespace App\Providers;

use App\Models\Producer;
use App\Models\Team;
use App\Observers\ProducerObserver;
use App\Observers\TeamObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

	protected $observers = [
		Team::class => [
			TeamObserver::class
		],
		Producer::class => [
			ProducerObserver::class
		],
	];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
