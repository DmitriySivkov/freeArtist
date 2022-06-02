<?php

namespace App\Http\Controllers;

use App\Contracts\Requests\UserRegisterRequestContract;
use App\Contracts\Services\UserRegisterServiceContract;

class RegisterController extends Controller
{
    public function store(UserRegisterRequestContract $request, UserRegisterServiceContract $registration)
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
