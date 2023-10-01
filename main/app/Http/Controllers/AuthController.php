<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	const TEST_CODE = 1111;

	public function login(Request $request, AuthService $authService)
    {
		$phone = $request->input('phone');
		$code = $request->input('code');
		$isAuth = $request->input('is_auth');

		if (!$phone) {
			throw new \Exception('Ошибка сервиса авторизации');
		}

		if (!$code) {
			return response()->json([
				'user_exists' => User::wherePhone($phone)->exists()
			]);
		}

		if ($code !== self::TEST_CODE) {
			throw new \Exception('Неверный код');
		}

		if (!$isAuth) {
			User::create([
				'phone' => $phone
			]);
		}

		return $authService->loginWithCredentials($phone);
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
}
