<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Services\AuthService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	public function login(Request $request, AuthService $authService)
    {
		if ($request->has(['phone', 'password']))
			return $authService->loginWithCredentials(
				$request->only(['phone', 'password']),
				$request->get('is_mobile')
			);

		return response('Ошибка сервера авторизации', 422);
    }

    /**
     * @return ResponseFactory|Response
     */
    public function logout()
    {
		if ($currentToken = request()->user()->currentAccessToken()) {
			$currentToken->delete();
		}

		Auth::guard('web')->logout();

        return response('Успешно', 200);
    }

	/**
	 * @param AuthService $authService
	 * @return false|\Illuminate\Http\JsonResponse
	 */
	public function viaToken(AuthService $authService)
	{
		return $authService->loginWithToken();
	}

	/**
	 * @param UserRegisterRequest $request
	 * @param AuthService $authService
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function register(UserRegisterRequest $request, AuthService $authService)
	{
		try {
			return $authService->register($request->validated(), $request->get('is_mobile'));
		} catch (\Throwable $e) {
			return response()->json([
				"errors" => [
					"registerService" => [$e->getMessage()]
				]
			])->setStatusCode(422);
		}
	}
}
