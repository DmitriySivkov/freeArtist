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

    public function prepareForValidation()
	{
		$this->merge([
			'user_id' => auth()->id(),
			'status' => Order::ORDER_STATUS_NEW,
		]);
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
				'required',
				'exists:users,id'
			],
			'producer_id' => [
				'required',
				'exists:producers,id'
			],
			'products.*.product_id' => [
				'required',
				'exists:products,id'
			],
			'products.*.amount' => [
				'required',
				'gt:0'
			],
			'payment_method' => [
				'required',
				'exists:payment_methods,id'
			],
			'status' => [
				'required'
			]
        ];
    }
}
