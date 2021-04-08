<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;

class BasePolicy
{
    protected function allow()
    {
        return Response::allow();
    }

    protected function deny($message = "This action is not permitted")
    {
        return Response::deny($message);
    }

    protected function authorize($result)
    {
        if($result) {
            return $this->allow();
        } else {
            return $this->deny();
        }
    }
}