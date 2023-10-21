<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\PaymentMethod;
use Illuminate\Http\Resources\Json\JsonResource;

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
			'order_products' => collect($this->order_products)->pluck('amount', 'product_id'),
			'payment_method' => PaymentMethod::PAYMENT_METHODS[$this->payment_method],
			'status' => $this->status,
			'user' => $this->user->name ?? $this->user->phone,
			'products' => collect($this->products)->map(
				fn($product) => [
					'id' => $product->id,
					'title' => $product->title
				]),
			'created_at' => $this->created_at->format('d-m-Y H:i')
		];
    }
}
