<?php

namespace App\Helpers;

use Laravel\Sanctum\PersonalAccessToken;

class TokenHelper
{
	public static function getUserByToken(string|null $hashedToken)
	{
		if (!$hashedToken) {
			return null;
		}

		$token = PersonalAccessToken::findToken($hashedToken);

		return $token->tokenable;
	}
}
