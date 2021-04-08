<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Authorization;

class AuthorizationTransformer extends TransformerAbstract
{
    public function transform(Authorization $authorization)
    {
        return $authorization->toArray();
    }
}
