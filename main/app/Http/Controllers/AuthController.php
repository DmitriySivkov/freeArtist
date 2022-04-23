<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{

    public function login(Request $request, AuthService $authService)
    {
        if ($request->has(['email', 'password']))
            return $authService->loginWithCredentials($request->only(['email', 'password']));

        if ($request->hasCookie('token'))
            return $authService->loginWithToken($request);

        return response('Ошибка сервера авторизации', 422);
    }

    /**
     * @param Request $request
     * @return ResponseFactory|Response
     *
     * its now logging out of current device.
     * Theres also a way to logout for all devices
     */
    public function logout(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $user->currentAccessToken()->delete();
        if ($request->hasHeader('Authorization'))
            $request->headers->remove('Authorization');

        /**
         * Cookie::forget is not used in this case because of required "sameSite=none" param
         */
        $cookie = cookie(
            'token', '', -1, null, null, true, true, false, 'none'
        );

        return response('logged out', 200)->withCookie($cookie);
    }

	public function hasTokenCookie(Request $request)
	{
		return $request->hasCookie('token');
	}

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
