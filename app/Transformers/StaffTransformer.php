<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Staff;

class StaffTransformer extends TransformerAbstract
{
    public function transform(Staff $staff)
    {
        return [
            'id' => $staff->id,
            'name' => $staff->name,
            'nip' => $staff->nip,
            'code' => $staff->code
        ];
    }
}
