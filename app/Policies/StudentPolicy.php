<?php

namespace App\Policies;

use App\Student;
use App\User;

class StudentPolicy extends BasePolicy
{
    public function update(User $user, Student $student)
    {
        // admin can update all student
        if($user->isAdmin()) {
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
        return $this->authorize($user->isAdmin());
    }

    public function delete(User $user)
    {
        // only admin can delete student
        if($user->isAdmin()) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}
