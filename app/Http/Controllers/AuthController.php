<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Students;
use App\Models\Staffs;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'username'    => 'required|max:255',
            'password' => 'required',
        ]);
        $data = $request->only('username', 'password');

        try {

            if (! $token = $this->jwt->attempt($data)) {
                return response()->json(['wrong combination of username & password'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['error' => 'could not create token']);

        }

        return response()->json(['data'=>
                                    [compact('token')]
                                ]);
    }

    public function logout(Request $request)
    {
        auth()->logout(true);
        return response()->json([
            'data' => [ 'status' => 'success',
                        'code' => '200']
        ], 200);
    }

    public function registerStudents(Request $request)
    {
        $this->validate($request, [
            'nim'       => 'required|max:16',
            'name'      => 'required|max:255',
            'gender'    => 'required',
            'religion'  => 'required',
        ]);


        $uuid = Str::orderedUuid();
        $newUser = [
            'username'  => $request->nim,
            'password'  => Hash::make('password'),
            'id'   => $uuid,
        ];
        $user = User::create($newUser);

        $id = Students::max('id') + 1;
        $newStudent = [
            'id'            => $id,
            'nim'           => $request->nim,
            'name'          => $request->name,
            'user_id'       => $uuid,
            'gender'        => $request->gender,
            'religion'      => $request->religion,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
        $student = Students::create($newStudent);



        // $user->assignRole('registered');

        return response()->json([
            'data' => [
                'message'   => 'Student registered',
                'status'    => 201],
            ], 201);
    }

    public function registerStaffs(Request $request)
    {
        $this->validate($request, [
            'nip'   => 'required|max:16',
            'name'  => 'required|max:255',
            'code'  => 'required',
        ]);

        $uuid = Str::orderedUuid();
        $newUser = [
            'username'  => $request->code,
            'password'  => Hash::make('password'),
            'id'   => $uuid,
        ];
        $user = User::create($newUser);

        $id = Staffs::max('id') + 1;
        $newStaff = [
            'nip'           => $request->nip,
            'name'          => $request->name,
            'user_id'       => $uuid,
            'created_at'    => Carbon::now(),
            'updated_at'    => Carbon::now(),
        ];
        $staff = Staffs::create($newStaff);
        // $user->assignRole('registered');

        return response()->json([
            'data' => [
                'message'   => 'Staff registered',
                'status'    => 201],
            ], 201);
    }

    public function me(Request $request)
    {
        return response()->json([auth()->user()]);
    }

    public function getAllUsersRole(Request $request)
    {
        $users = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('users.name as name', 'roles.name as role')
                ->get();

        return response()->json([$users
        ]);
    }
}
