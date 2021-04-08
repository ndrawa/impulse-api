<?php

namespace App\Http\Controllers\Api\V1\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Staff;
use App\Transformers\StaffTransformer;
use Illuminate\Validation\Rule;


class StaffController extends BaseController
{
    public function index(Request $request)
    {
        $staffs = Staff::query();
        $per_page = env('PAGINATION_SIZE', 15);
        $request->whenHas('per_page', function($size) use (&$per_page) {
            $per_page = $size;
        });

        $request->whenHas('search', function($search) use (&$staffs) {
            $staffs = $staffs->where('name', 'LIKE', '%'.$search.'%');
        });

        $staffs = $staffs->paginate($per_page);

        return $this->response->paginator($staffs, new StaffTransformer);
    }

    public function show(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        return $this->response->item($staff, new StaffTransformer);
    }

    public function create(Request $request)
    {
        $this->authorize('create', Staff::class);
        $this->validate($request, [
            'name' => 'required',
            'nip' => [
                'required',
                Rule::unique('staffs')
            ],
            'code' => [
                'required',
                Rule::unique('staffs')
            ]
        ]);
        $staff = Staff::create($request->all());

        return $this->response->item($staff, new StaffTransformer);
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $this->authorize('update', $staff);
        $this->validate($request, [
            'name' => 'required',
            'nip' => [
                'required',
                Rule::unique('staffs')->ignore($staff->nip, 'nip')
            ],
            'code' => [
                'required',
                Rule::unique('staffs')->ignore($staff->code, 'code')
            ]
        ]);
        $staff->fill($request->all());
        $staff->save();

        return $this->response->item($staff, new StaffTransformer);
    }

    public function delete(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $this->authorize('delete', $staff);
        $staff->delete();

        return $this->response->noContent();
    }
}