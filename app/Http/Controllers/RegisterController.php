<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\ClientRepository;
use Symfony\Component\Console\Exception\CommandNotFoundException;

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

            DB::commit();
        } catch (QueryException $e) {
            DB::rollBack();
            return response($e->getMessage(), $e->getCode());
        }

        return $user;
    }
}
