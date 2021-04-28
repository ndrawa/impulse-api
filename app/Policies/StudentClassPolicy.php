<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentClassPolicy extends BasePolicy
{
    public function update(User $user)
    {
        // Laboran can update all rooms
        if($user->isLaboran()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }

    public function create(User $user)
    {
        // only laboran can create rooms
        return $this->authorize($user->isLaboran());
    }

    public function delete(User $user)
    {
        // only labran can delete room
        if($user->isLaboran()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}
