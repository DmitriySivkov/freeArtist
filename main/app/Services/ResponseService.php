<?php

namespace App\Services;

class ResponseService
{
	/**
	 * @param $message
	 * @return \Illuminate\Http\JsonResponse
	 */
	public static function error($message)
	{
		return response()->json(["errors" => $message])->setStatusCode(422);
	}
}
