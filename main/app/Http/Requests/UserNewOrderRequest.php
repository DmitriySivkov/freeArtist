<?php

namespace App\Http\Requests;

use App\Contracts\Requests\NewOrderRequestContract;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class UserNewOrderRequest extends FormRequest implements NewOrderRequestContract
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

    /** TODO payment method and status */
    public function prepareForValidation()
	{
		$this->merge([
			'user_id' => auth('sanctum')->user()->id,
			'payment_method' => Order::ORDER_PAYMENT_METHOD_CARD,
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
			'cart.*.products' => ['required'],
			'cart.*.products.*.amount' => ['gt:0'],
			'cart.*.products.*.data.id' => ['exists:products,id'],
			'payment_method' => ['required'],
			'status' => ['required']
        ];
    }
}
