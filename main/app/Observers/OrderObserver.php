<?php

namespace App\Observers;

use App\Events\ProducerOrderCreated;
use App\Events\ProducerOrderUpdated;
use App\Models\Order;

class OrderObserver
{
	public function created(Order $order)
	{
		ProducerOrderCreated::dispatch($order);
	}

    public function updated(Order $order)
	{
		ProducerOrderUpdated::dispatch($order);
	}
}
