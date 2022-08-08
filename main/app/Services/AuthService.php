<?php


namespace App\Services;


use App\Models\Producer;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Services\RelationRequests\ProducerRelationRequestService;
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

		$urrService = new UserRelationRequestService;
		$urrService->setUser($this->user);
		$this->user->outgoing_coworking_requests = $urrService->getOutgoingCoworkingRequests();

		$prrService = new ProducerRelationRequestService;
		$this->user->teams->map(function(Team $team) use ($prrService) {
			if ($team->detailed_type === Producer::class) {
				$prrService->setProducer($team->detailed);
				$team->requests = collect(['total_request_count' => 0]);
				if (
					$this->user->owns($team) ||
					$this->user->hasPermission("producer_incoming_coworking_requests", $team->name)
				) {
					$team->requests['incoming_coworking_requests'] = $prrService->getIncomingCoworkingRequests();
					$team->requests['total_request_count'] =
						$team['total_request_count'] + count($team->requests['incoming_coworking_requests']);
				}
			}
			return $team;
		});
	}

}
