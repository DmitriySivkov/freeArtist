<?php

namespace App\Observers;

use App\Events\ProducerUpdated;
use App\Models\Producer;

class ProducerObserver
{
    /**
     * @param  \App\Models\Producer  $producer
     * @return void
     */
    public function updated(Producer $producer)
    {
        ProducerUpdated::dispatch(
			$producer->getDirty(),
			$producer->team->id,
			$producer->id
		);
    }
}
