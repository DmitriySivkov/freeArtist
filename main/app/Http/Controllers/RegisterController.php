<?php

namespace App\Http\Controllers;

use App\Contracts\Requests\RegisterRequestContract;
use App\Contracts\Services\RegisterServiceContract;

class RegisterController extends Controller
{
    public function store(RegisterRequestContract $request, RegisterServiceContract $registration)
    {
		try {
			return $registration->register($request->validated());
		} catch (\Throwable $e) {
			return response()->json(["errors" =>
				["registerService" => [$e->getMessage()]]
			])
				->setStatusCode(422);
		}
    }
}
