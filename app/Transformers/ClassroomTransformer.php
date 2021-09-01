<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Classroom;

class ClassroomTransformer extends TransformerAbstract
{
    public function transform(Classroom $classroom)
    {
        return [
            'id' => $classroom->id,
            'name' => $classroom->name
        ];
    }
}


