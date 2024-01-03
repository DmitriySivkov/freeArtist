<?php

namespace App\Http\Controllers;

use App\Enums\PaymentProviderEnum;
use App\Models\Producer;
use App\Models\ProducerPaymentProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ProducerPaymentProviderController extends Controller
{
	public function index(Producer $producer)
	{
		//Crypt::decryptString();
	}

    public function store(Producer $producer, Request $request)
	{
		$paymentProviderId = $request->input('payment_provider_id');
		$paymentProviderData = $request->input('payment_provider_data');

		foreach ($paymentProviderData as $value) {
			if ($secretKey = PaymentProviderEnum::getPaymentProviderSecretKey($paymentProviderId)) {
				$paymentProviderData[$secretKey] = Crypt::encryptString($value);
			}
		}

		ProducerPaymentProvider::create([
			'producer_id' => $producer->id,
			'payment_provider_id' => $paymentProviderId,
			'payment_provider_data' => $paymentProviderData
		]);
	}
}
