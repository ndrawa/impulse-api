<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\User;

class CoursePolicy extends BasePolicy
{
    public function update(User $user)
    {
        // Laboran can update all course
        if($user->isLaboran()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }

    public function create(User $user)
    {
        // only laboran can create course
        return $this->authorize($user->isLaboran());
    }

    public function delete(User $user)
    {
        // only labran can delete course
        if($user->isLaboran()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}
