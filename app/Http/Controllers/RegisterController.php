<?php

namespace App\Http\Controllers;

use App\Models\User;

class RegisterController extends Controller
{
    public function store()
    {
        $attrs = request()->all();
        User::create([
            'name' => $attrs['name']['inputValue'],
            'email' => $attrs['email']['inputValue'],
            'password' => $attrs['password']['inputValue'],
            'type' => $attrs['role']['modelValue']
        ]);
    }
}
