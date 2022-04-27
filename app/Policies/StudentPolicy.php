<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy extends BasePolicy
{
    public function update(User $user, Student $student)
    {
        // admin can update all student
        if($user->isLaboran() || $user->isAslab()) {
            return $this->allow();
        } else if($user->id == $student->user->id) {
            // only the logged user can update himself
            return $this->allow();
        } else {
            return $this->deny();
        }
    }

    public function create(User $user)
    {
        // only admin can create student
        return $this->authorize($user->isLaboran() || $user->isAslab());
    }

    public function delete(User $user)
    {
        // only admin can delete student
        if($user->isLaboran() || $user->isAslab()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}
