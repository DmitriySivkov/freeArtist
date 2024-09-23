<?php

namespace App\Http\Controllers;

use App\Enums\PaymentProviderEnum;
use App\Models\Permission;
use App\Models\Producer;
use App\Models\ProducerPaymentProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ProducerPaymentProviderController extends Controller
{
	public function index(Producer $producer)
	{
		/** @var User $user */
		$user = auth()->user();

		if (
			!$user->owns($producer->team) &&
			!$user->hasPermission(
				Permission::PERMISSION_PRODUCER_PAYMENT_METHODS['name'],
				$producer->team->name
			)
		)
		{
			throw new \LogicException('Недостаточно прав');
		}

		foreach ($producer->paymentProviders as $paymentProvider) {
			if ($secretKey = PaymentProviderEnum::getPaymentProviderSecretKey($paymentProvider->payment_provider_id)) {
				$paymentProvider->setAttribute(
					'payment_provider_data',
					array_merge(
						$paymentProvider->payment_provider_data,
						[$secretKey => Crypt::decryptString($paymentProvider->payment_provider_data[$secretKey])]
					)
				);
			}
		}

		return $producer->paymentProviders;
	}

    public function store(Producer $producer, Request $request)
	{
		$paymentProviderId = $request->input('payment_provider_id');
		$paymentProviderData = $request->input('payment_provider_data');
		$paymentProviderActivity = $request->input('payment_provider_activity');

		foreach ($paymentProviderData as $value) {
			if ($secretKey = PaymentProviderEnum::getPaymentProviderSecretKey($paymentProviderId)) {
				$paymentProviderData[$secretKey] = Crypt::encryptString($value);
			}
		}

		if ((bool)$paymentProviderActivity === true) {
			$producer->paymentProviders()
				->where('payment_provider_id', '!=', $paymentProviderId)
				->update([
					'is_active' => false
				]);
		}

		return ProducerPaymentProvider::updateOrCreate(
			[
				'producer_id'			=> $producer->id,
				'payment_provider_id'	=> $paymentProviderId
			],
			[
				'payment_provider_data'	=> $paymentProviderData,
				'is_active'				=> $paymentProviderActivity
			]
		);
	}
}
