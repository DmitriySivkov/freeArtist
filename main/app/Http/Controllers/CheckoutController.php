<?php

namespace App\Http\Controllers;

use App\Models\Producer;

class CheckoutController extends Controller
{
    public function load(Producer $producer)
	{
		$paymentMethods = $producer->paymentMethods;

		return [
			'payment_methods' => $paymentMethods
		];
	}
}
