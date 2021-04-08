<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        $data = [];
        if($user->isStaff()) {
            $transformer = new StaffTransformer;
            $data = $transformer->transform($user->staff);
        } else {
            $transformer = new StudentTransformer;
            $data = $transformer->transform($user->student);
        }
        $data["roles"] = $user->getRoleNames();
        $data["username"] = $user->username;

        return $data;
    }
}
