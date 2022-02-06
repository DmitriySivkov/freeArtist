<?php

namespace App\Http\Controllers;

use App\Contracts\Requests\UserRegisterRequestContract;
use App\Contracts\Services\UserRegisterServiceContract;

class RegisterController extends Controller
{
    public function store(UserRegisterRequestContract $request, UserRegisterServiceContract $registration)
    {
		return $registration->run($request);
    }
}
