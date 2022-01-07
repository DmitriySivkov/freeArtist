<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __invoke(Request $request)
    {
      /** @var User $user */
      $user = User::where('email', $request->email)->first();
      $arError = ['errors' => ['total' => ['Неверные почта или пароль']]];
      if (!$user) {
          return response($arError, 422);
      }

      $isValidCredentials = Hash::check($request['password'], $user->getAuthPassword());

      if (!$isValidCredentials) {
          return response($arError, 422);
      }
      $tokens = $user->tokens()->get();
      setcookie('access-token', serialize($tokens), 0, "", "", false, true);
      $user->tokens = $tokens;
      return $user;
    }

}
