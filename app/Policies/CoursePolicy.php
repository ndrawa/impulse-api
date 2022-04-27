<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\User;

class CoursePolicy extends BasePolicy
{
    public function update(User $user)
    {
        // Laboran can update all course
        if($user->isLaboran() || $user->isAslab()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }

    public function create(User $user)
    {
        // only laboran can create course
        return $this->authorize($user->isLaboran() || $user->isAslab());
    }

    public function delete(User $user)
    {
        // only labran can delete course
        if($user->isLaboran() || $user->isAslab()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}
