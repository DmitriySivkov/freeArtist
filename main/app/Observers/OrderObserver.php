<?php

namespace App\Observers;

use App\Events\ProducerOrderUpdated;
use App\Models\Order;

class OrderObserver
{
    public function updated(Order $order)
	{
		ProducerOrderUpdated::dispatch($order);
	}
}
