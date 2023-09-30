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
            'user_id' => ['required', 'exists:users,id'],
			'cart.*.producer.id' => ['required', 'exists:producers,id'],
			'cart.*.product_list' => ['required'],
			'cart.*.product_list.*.amount' => ['gt:0'],
			'cart.*.product_list.*.data.id' => ['exists:products,id'],
			'payment_method' => ['required'],
			'status' => ['required']
        ];
    }
}
