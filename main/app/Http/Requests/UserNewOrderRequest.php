<?php

namespace App\Http\Requests;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
			'transaction_uuid' => [
				Rule::requiredIf(fn() => $this->payment_method !== PaymentMethod::PAYMENT_METHOD_CASH_ID),
			],
			'producer_id' => [
				Rule::requiredIf(fn() => $this->payment_method === PaymentMethod::PAYMENT_METHOD_CASH_ID),
			],
			'order_date' => [
				'required',
				'date'
			],
			'payment_method' => [
				'required'
			],
			'meta' => [
				'nullable'
			]
        ];
    }
}
