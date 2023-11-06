<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UserNewOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

	/**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => [
				'nullable'
			],
			'producer_id' => [
				'required',
				'exists:producers,id'
			],
			'order_products.*.product_id' => [
				'required',
				'exists:products,id'
			],
			'order_products.*.amount' => [
				'required',
				'gt:0'
			],
			'payment_method' => [
				'required',
				'exists:payment_methods,id'
			],
			'meta' => [
				'nullable'
			]
        ];
    }
}
