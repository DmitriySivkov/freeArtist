<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthCookieService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->all(['email', 'password']);

        if (!auth()->attempt($credentials)) {
          return response()->json(['errors' => ['total' => ['Неверные почта или пароль']]], 422);
        }

        /** @var User $user */
        $user = auth()->user();

        $token = $user->createToken('web-app')->accessToken;

        /**
         * mandatory for cross-site: SSL; headers: secure=true;sameSite=none
         */
        $authCookie = new AuthCookieService(
            'token', $token, 0,
            null, null, true, true, false, 'none'
        );

        return response($user)->withCookie($authCookie->get());
    }

    public function logout(Request $request)
    {
        /** @var User $user */
        $user = auth()->user()->token();
        $user->revoke();
        return response('logged out', 200);
    }

}
