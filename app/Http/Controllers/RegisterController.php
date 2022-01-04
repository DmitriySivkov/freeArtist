<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        return User::create($request->validated());
    }
}
