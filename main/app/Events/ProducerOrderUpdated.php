<?php

namespace App\Events;

use App\Http\Resources\ProducerOrdersResource;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProducerOrderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	public string $queue;

	private Order $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
		$this->queue = config('queue.broadcast');

        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("producers.{$this->order->producer_id}.orders");
    }

	public function broadcastAs()
	{
		return "order.updated";
	}

	public function broadcastWith()
	{
		return [
			'model' => new ProducerOrdersResource($this->order)
		];
	}
}
