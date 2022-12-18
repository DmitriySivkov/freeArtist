<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
	/**
	 * @param Request $request
	 * @param AuthService $authService
	 * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|\Illuminate\Http\JsonResponse|Response
	 */
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
		if ($currentToken = request()->user()->currentAccessToken())
			$currentToken->delete();

		Auth::guard('web')->logout();

        return response('Успешно', 200);
    }

	/**
	 * @param AuthService $authService
	 * @return false|\Illuminate\Http\JsonResponse
	 */
	public function authViaToken(AuthService $authService)
	{
		if (!auth('sanctum')->user())
			return false;

		return $authService->loginWithToken();
	}

	/**
	 * @param Request $request
	 * @return bool|\Illuminate\Contracts\Foundation\Application|ResponseFactory|Response|int
	 */
	public function verifyEmail(Request $request)
    {
        if (!hash_equals(
            (string) $request->get('hash'),
            sha1($request->get('email')))
        )
           return response('Не удалось верифицировать почту', 422);

        return User::where('email', $request->get('email'))->update([
            'email_verified_at' => Carbon::now()
        ]);
    }

}
