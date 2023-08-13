<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbqueries\UsersModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade\Log;

class UsersController extends Controller
{
    private $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }

    public function index(Request $request)
    {
        if(!Auth::check() && $request->path() != 'login'){
            return redirect('/login');
        }

        if(!Auth::check() && $request->path() == 'login'){
            return view('welcome');
        }

        $user = Auth::user();
        if($request->path() == 'login'){
            return redirect('/');
        }

        return $this->checkPermissions($user, $request);
    }

    public function checkPermissions($user, $request){

        $permissions   = json_decode($user->role->permission);
        $hasPermission = false;

        $main_path = $request->segment(1);

        if($main_path == '') {
            $main_path = '/';
        } else {
            $main_path = '/'.$main_path;
        }

        if(!is_null($permissions)){
            foreach($permissions as $permission){
                if($permission->name == $main_path) {
                    if($permission->read) {
                        $hasPermission = true;
                    }
                }
            }
        } else {
            return view('welcome');
        }

        if($hasPermission === true) {
            return view('welcome');
        } else {
            return view("notfound");
        }

    }

    public function getUsers()
    {
        return DB::table("users as u")
            ->select("u.*", "r.roleName")
            ->leftJoin("roles as r", "r.id", "=", "u.role_id")
            ->orderBy("u.fullName")
            ->get();
    }
    public function getUser($user_id)
    {
        return DB::table("users as u")
            ->select("u.id", "u.fullName", "u.username", "u.role_id", "u.phone", "r.roleName")
            ->leftJoin("roles as r", "r.id", "=", "u.role_id")
            ->where("u.id", $user_id)
            ->first();
    }

    public function addUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => "required",
            'username' => "required|unique:users",
            'password' => "bail|required|min:3",
            'role_id'  => "required",
        ]);

        $password = bcrypt($request->password);
        $arr = [
            'fullName' => $request->fullName,
            'password' => $password,
            'username' => $request->username,
            'role_id'  => $request->role_id,
            'address'  => $request->address,
            'phone'    => $request->phone,
        ];

        return $this->usersModel->addUser($arr);

    }

    public function editUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => "required",
            'username' => "required|unique:users,username,$request->id",
            'role_id' => "required",
        ]);

        if(!is_null($request->password)) {
            $this->validate($request, [
                'password' => "min:3",
            ]);
        }

        $arr = [
            'fullName' => $request->fullName,
            'username' => $request->username,
            'role_id' => $request->role_id,
        ];

        if(!empty($request->password)){
            $password = bcrypt($request->password);
            $arr["password"] = $password;
        }

        return $this->usersModel->update($request->id, $arr);
    }

    public function deleteUser(Request $request)
    {
        $this->validate($request, ['id' => "required",]);

        return $this->usersModel->delete($request->id);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => "bail|required|min:3",
        ]);

        if (Auth::attempt(["username" => $request->username, "password" => $request->password])) {
            $user = Auth::user();
            if($user->role->isAdmin == 0) {
                Auth::logout();
                return response()->json([
                    'msg' => "Username yoki parolingiz not'g'ri!"
                ], 401);
            }

            return response()->json([
                'msg' => 'Tizimga muvaffaqiyatli kirdingiz!',
                'user' => $user
            ]);
        } else {
            return response()->json([
                'msg' => "Username yoki parolingiz not'g'ri!"
            ], 401);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    public function getRoles(){
        return DB::table("roles")->get();
    }

    public function getRole($id){
        return Role::where("id", $id)->first();
    }

    public function addRole(Request $request){
        $this->validate($request, [
            'roleName' => 'required'
        ]);

        $role = Role::create([
            'roleName' => $request->roleName
        ]);

        return $role;
    }

    public function editRole(Request $request){
        $this->validate($request, [
            'roleName' => 'required'
        ]);

        $res = Role::where("id", $request->id)->update(['roleName' => $request->roleName]);

        return $this->getRole($request->id);
    }

    public function deleteRole(Request $request){
        $this->validate($request, ['id' => "required"]);

        return Role::where('id', $request->id)->delete();
    }

    public function assignRoles(Request $request){
        $this->validate($request, [
            'permission'=> 'required',
            'id'        => 'required'
        ]);

        return Role::where("id", $request->id)->update([
            'permission' => $request->permission
        ]);
    }

    public function loggedUser(){
        $authUser = Auth::user();

        return $this->getUser($authUser->id);
    }
}
