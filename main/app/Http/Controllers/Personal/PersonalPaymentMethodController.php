<?php

namespace App\Http\Controllers\Personal;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Http\Request;

class PersonalPaymentMethodController extends Controller
{
	/**
	 * @param Producer $producer
	 * @return \Illuminate\Support\Collection
	 */
	public function getPaymentMethods(Producer $producer)
	{
		return $producer->paymentMethods->pluck('id');
	}

	/**
	 * @param Request $request
	 * @param Producer $producer
	 * @return void
	 */
	public function setPaymentMethods(Request $request, Producer $producer)
	{
		// todo - check for rights & check that at least 1 payment method exists
		$paymentMethods = $request->input('payment_methods');

		$producer->paymentMethods()->sync($paymentMethods);
	}
}
