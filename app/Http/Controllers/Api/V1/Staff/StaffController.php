<?php

namespace App\Http\Controllers\Api\V1\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V1\BaseController;
use App\Models\Staff;
use App\Models\User;
use App\Imports\StaffImport;
use App\Transformers\StaffTransformer;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

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
            $users = $users->where('name', 'ILIKE', '%'.$search.'%')
                            ->orWhere('nip', 'ILIKE', '%'.$search.'%')
                            ->orWhere('code', 'ILIKE', '%'.$search.'%');
        });

        if($request->has('orderBy') && $request->has('sortedBy')) {
            $orderBy = $request->get('orderBy');
            $sortedBy = $request->get('sortedBy');
            $staffs->orderBy($orderBy, $sortedBy);
        } 
        else if($request->has('orderBy')) {
            $orderBy = $request->get('orderBy');
            $staffs->orderBy($orderBy);
        }

        $staffs = $staffs->paginate($per_page);

        return $this->response->paginator($staffs, new StaffTransformer);
    }

    public function getall(Request $request)
    {
        $staff = Staff::query();

        if ($request->has('search')) {
            $request->whenHas('search', function($search) use (&$staff) {
                $staff = $staff->where('id', 'LIKE', '%'.$search.'%')->get();
            });
        } else {
            $staff = Staff::get();
        }

        return $this->response->item($staff, new StaffTransformer);
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
        $user = User::findOrFail($staff->user_id);
        $this->authorize('delete', $staff);
        $user->delete();

        return $this->response->noContent();
    }

    public function import(Request $request)
    {
        Excel::import(new StaffImport, request()->file('file'));
        return "import success";
    }
}