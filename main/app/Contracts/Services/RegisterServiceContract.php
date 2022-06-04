<?php


namespace App\Contracts\Services;


use App\Contracts\Requests\RegisterRequestContract;

interface RegisterServiceContract
{
	public function register(RegisterRequestContract $request);
}
