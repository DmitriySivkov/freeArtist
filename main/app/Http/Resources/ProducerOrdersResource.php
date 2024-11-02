<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/** @mixin Order */
class ProducerOrdersResource extends JsonResource
{
	public static $wrap = null;

	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
    public function toArray($request)
    {
        return [
			'id' => $this->id,
			'producer_order_id' => $this->producer_order_id,
			'producer_id' => $this->producer_id,
			'assignee' => $this->assignee?->only(['id', 'name', 'phone']),
			'order_products' => collect($this->order_products)->pluck('amount', 'id'),
			'payment_method' => PaymentMethod::PAYMENT_METHODS[$this->transaction->payment_method],
			'status' => $this->status,
			'user' => $this->user ? ($this->user->name ?? $this->user->phone) : $this->transaction->phone,
			'products' => collect($this->products)->map(
				fn($product) => [
					'id' => $product->id,
					'title' => $product->title
				]),
			'prepare_by' => $this->prepare_by,
			'created_at' => $this->created_at->format('d.m.Y H:i'),
		];
    }
}
