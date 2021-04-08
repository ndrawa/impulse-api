<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Staff;

class StaffPolicy extends BasePolicy
{

    public function update(User $user, Staff $staff)
    {
        // admin can update all staff
        if($user->isAdmin()) {
            return $this->allow();
        } else if($user->id == $staff->user->id) {
            // only the logged user can update himself
            return $this->allow();
        } else {
            return $this->deny();
        }
    }

    public function create(User $user)
    {
        // only admin can create staff
        return $this->authorize($user->isAdmin());
    }

    public function delete(User $user, Staff $staff)
    {
        // only admin can delete staff & admin can't delete himself
        if($user->isAdmin() && $user->id != $staff->user->id) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}