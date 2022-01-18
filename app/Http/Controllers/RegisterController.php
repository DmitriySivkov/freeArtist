<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Jobs\SendMailJob;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            /** @var User $user */
            $user = User::create($request->validated());

            $clientRepository = new ClientRepository();
            $clientRepository->create($user->id, $request->header('X-APP-TYPE'),'', null, true);

            SendMailJob::dispatch($user);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response($e->getMessage(), $e->getCode());
        }

        return $user;
    }
}
