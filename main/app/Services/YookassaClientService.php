<?php

namespace App\Services;

use App\Contracts\YookassaClientServiceContract;

class YookassaClientService implements YookassaClientServiceContract
{
	private $shopId = '';
	private $secretKey = '';

	public function getClient()
	{
		if (!$this->shopId || !$this->secretKey) {
			throw new \LogicException('shop_id or secret_key is not set');
		}

		$client = new \YooKassa\Client();
		$client->setAuth($this->shopId, $this->secretKey);

		return $client;
	}

	public function setShopId($shopId)
	{
		$this->shopId = $shopId;
	}

	public function setSecretKey($secretKey)
	{
		$this->secretKey = $secretKey;
	}
}
