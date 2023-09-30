<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class ProducerNewOrderRequest extends FormRequest
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
			'payment_method' => 1
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
			'user_id' => ['required'],
			'producer_id' => ['required'],
			'products' => [],
			'payment_method' => ['required'],
			'status' => ['required']
		];
	}
}
