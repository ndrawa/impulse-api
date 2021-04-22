<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\Room;

class RoomTransformer extends TransformerAbstract
{
    public function transform(Room $room)
    {
        return [
            'id' => $room->id,
            'name' => $room->name,
            'desc' => $room->desc,
            'msteam_code' => $room->msteam_code,
            'msteam_link' => $room->msteam_link
        ];
    }
}


