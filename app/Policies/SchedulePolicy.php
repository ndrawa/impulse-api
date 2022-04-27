<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy extends BasePolicy
{
    public function update(User $user)
    {
        // Laboran can update all schedules
        if($user->isLaboran() || $user->isAslab()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }

    public function create(User $user)
    {
        // only laboran can create schedules
        return $this->authorize($user->isLaboran() || $user->isAslab());
    }

    public function delete(User $user)
    {
        // only labran can delete schedule
        if($user->isLaboran() || $user->isAslab()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}
