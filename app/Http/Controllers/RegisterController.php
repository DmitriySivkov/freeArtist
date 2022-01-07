<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        /** @var User $user */
        $user = User::create($request->validated());

        $user->createToken('web-app')->accessToken;

        return $user;
    }
}
