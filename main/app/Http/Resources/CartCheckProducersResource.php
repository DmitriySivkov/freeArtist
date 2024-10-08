<?php

namespace App\Http\Resources;

use App\Models\Producer;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Producer */
class CartCheckProducersResource extends JsonResource
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
			'display_name' => $this->team->display_name,
			'payment_methods' => $this->paymentMethods->makeHidden(['created_at', 'updated_at']),
			'payment_provider_id' => $this->activePaymentProvider?->payment_provider_id
		];
    }
}
