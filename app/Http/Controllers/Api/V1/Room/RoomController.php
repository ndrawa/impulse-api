<?php

namespace App\Http\Controllers\Api\V1\Room;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Room;
use App\Transformers\RoomTransformer;
use Illuminate\Validation\Rule;

class RoomController extends BaseController
{
    public function index(Request $request)
    {
        $rooms = Room::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$staffs) {
            $rooms = $rooms->where('name', 'LIKE', '%'.$search.'%');
        });

        $rooms = $rooms->paginate($per_page);

        return $this->response->paginator($rooms, new RoomTransformer);
    }

    // without paginator
    // public function rooms(Request $request)
    // {
    //     $rooms = Room::get();

    //     return $this->response->collection($rooms, new RoomTransformer);
    // }
}
