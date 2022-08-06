<?php


namespace App\Services;


use App\Models\User;
use App\Services\RelationRequests\UserRelationRequestService;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
	protected ?User $user;

	/**
	 * @param $credentials
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithCredentials($credentials)
    {
        if (!auth()->attempt($credentials))
            return response()->json(['errors' => ['total' => ['Неверный телефон или пароль']]], 422);

        $this->user = auth()->user();
        $abilities = ['*'];

		$loginToken = PersonalAccessToken::query()->where('tokenable_id', $this->user->id)
			->where('tokenable_type', User::class)->first();

		if ($loginToken) {
			$abilities = $loginToken->abilities;
			$loginToken->delete();
		}

		$token = $this->user->createToken($this->user->phone, $abilities)
			->plainTextToken;

		$this->getUserPayload();

        return response()->json($this->user)
			->withCookie(
				cookie('token', $token, 0, null, null, true, true, false, 'none')
			);
    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function loginWithToken(Request $request)
    {
        if (
			!$request->hasHeader('Authorization') ||
			$request->header('Authorization') !== 'Bearer ' . $request->cookie('token')
		)
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));

        $this->user = auth("sanctum")->user();

		$this->getUserPayload();

        return response()->json($this->user);
    }

	/**
	 * @return void
	 */
	private function getUserPayload()
	{
		$this->user->load([
			'roles',
		]);
		$this->user->teams = $this->user->allTeams();
		$this->user->outgoing_coworking_requests = (new UserRelationRequestService($this->user))
			->getOutgoingCoworkingRequests();
	}
	//figure how to load team's relation requests (just check on component load ?)
}
