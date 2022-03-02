<?php


namespace App\Contracts\Services;


use App\Contracts\Requests\UserRegisterRequestContract;

interface UserRegisterServiceContract
{
	public function run(UserRegisterRequestContract $request);
}
