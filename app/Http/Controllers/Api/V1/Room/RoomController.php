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
        $rooms = Room::query()->get();
        // $per_page = env('PAGINATION_SIZE', 15);
        // $request->whenHas('per_page', function($size) use (&$per_page) {
        //     $per_page = $size;
        // });

        // $request->whenHas('search', function($search) use (&$staffs) {
        //     $rooms = $rooms->where('name', 'LIKE', '%'.$search.'%');
        // });

        // $rooms = $rooms->paginate($per_page);

        return $this->response->item($rooms, new RoomTransformer);
    }

    public function show(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        return $this->response->item($room, new RoomTransformer);
    }

    public function create(Request $request)
    {
        // $this->authorize('create', Room::class);
        $this->validate($request, [
            'name' => 'required',
            'desc' => 'required',
            'msteam_code' => 'required',
            'msteam_link' => 'required'
        ]);
        $room = Room::create($request->all());

        return $this->response->item($room, new RoomTransformer);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        // $this->authorize('update', $room);
        $this->validate($request, [
            'name' => 'required',
            'desc' => 'required',
            'msteam_code' => 'required',
            'msteam_link' => 'required'
        ]);
        $room->fill($request->all());
        $room->save();

        return $this->response->item($room, new RoomTransformer);
    }

    public function delete(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        // $this->authorize('delete', $room);
        $room->delete();

        return $this->response->noContent();
    }
    // without paginator
    // public function rooms(Request $request)
    // {
    //     $rooms = Room::get();

    //     return $this->response->collection($rooms, new RoomTransformer);
    // }
}
