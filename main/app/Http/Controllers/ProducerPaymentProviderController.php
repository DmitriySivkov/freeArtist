<?php

namespace App\Http\Controllers;

use App\Enums\PaymentProviderEnum;
use App\Models\Producer;
use App\Models\ProducerPaymentProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ProducerPaymentProviderController extends Controller
{
	public function index(Producer $producer, Request $request)
	{
		$paymentProviderId = $request->input('payment_provider_id');

		$paymentProvider = ProducerPaymentProvider::where('producer_id', $producer->id)
			->where('payment_provider_id', $paymentProviderId)
			->first();

		if (!$paymentProvider) {
			return false;
		}

		if ($secretKey = PaymentProviderEnum::getPaymentProviderSecretKey($paymentProviderId)) {
			$paymentProvider->setAttribute(
				'payment_provider_data',
				array_merge(
					$paymentProvider->payment_provider_data,
					[$secretKey => Crypt::decryptString($paymentProvider->payment_provider_data[$secretKey])]
				)
			);
		}

		return $paymentProvider->payment_provider_data;
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
