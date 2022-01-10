<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CookieService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function auth(Request $request)
    {
        $credentials = $request->all(['email', 'password']);

        if (!auth()->attempt($credentials)) {
          return response()->json(['errors' => ['total' => ['Неверные почта или пароль']]], 422);
        }

        /** @var User $user */
        $user = auth()->user();

        /**
         * mandatory for cross-site: SSL; headers: secure=true;sameSite=none
         */
        $authCookie = new CookieService(
            'tokens', $user->getSerializedAccessTokens(), 0,
            null, null, true, true, false, 'none'
        );

        return response($user)->withCookie($authCookie->getCookie());
    }

    public function logout()
    {
        return auth()->logout();
    }

}
