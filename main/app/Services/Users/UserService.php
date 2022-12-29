<?php

namespace App\Services\Users;

use App\Contracts\UserServiceContract;
use App\Models\Producer;
use App\Models\User;

class UserService implements UserServiceContract
{
    protected User $user;

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOutgoingCoworkingRequests()
    {
        return $this->user->outgoingRelationRequests()
            ->get()
            ->loadMorph('to', [
                Producer::class => ['team']
            ]);
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
