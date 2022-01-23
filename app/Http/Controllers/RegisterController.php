<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Jobs\SendEmailVerificationJob;
use App\Models\UserModel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            /** @var UserModel $user */
            $user = UserModel::create($request->validated());

            $clientRepository = new ClientRepository();
            $clientRepository->create($user->id, $request->header('X-APP-TYPE'),'', null, true);

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response($e->getMessage(), $e->getCode());
        }

        SendEmailVerificationJob::dispatch($user);

        return $user;
    }
}
