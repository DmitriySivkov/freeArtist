<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	/**
	 * @param Request $request
	 * @param AuthService $authService
	 * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|\Illuminate\Http\JsonResponse|Response
	 */
	public function login(Request $request, AuthService $authService)
    {
		$phone = $request->input('phone');
		$isMobile = $request->input('is_mobile');

		if (!$phone) {
			return response('Ошибка сервера авторизации', 422);
		}

		return $authService->loginWithCredentials($phone, $isMobile);
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
		if (!auth()->user()) {
			return false;
		}

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
