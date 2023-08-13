<?php

namespace App\Http\Controllers\dbqueries;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersModel extends Controller
{

    public function getAdmins()
    {
        return DB::table("users as u")
        ->select("u.*", "r.roleName", "s.name as storeName")
        ->leftJoin("roles as r", "r.id", "=", "u.role_id")
        ->leftJoin("stores as s", "s.id", "=", "u.store_id")
        ->where("r.isAdmin", 1)
        ->orderBy("u.fullName")
        ->get();
    }

    public function getUser($user_id)
    {
        return DB::table("users as u")
        ->select("u.id", "u.fullName", "u.username", "u.role_id", "u.store_id", "u.phone", "u.address", "r.roleName")
        ->leftJoin("roles as r", "r.id", "=", "u.role_id")
        ->where("u.id", $user_id)
        ->first();
    }

    public function getClients()
    {
        return DB::table("users as u")
        ->select("u.*", "r.roleName")
        ->leftJoin("roles as r", "r.id", "=", "u.role_id")
        ->where("r.isAdmin", 0)
        ->orderBy("u.fullName")
        ->get();
    }

    public function editClient($id, $arr) {
        User::where('id', $id)->update($arr);

        return $this->getUser($id);
    }

    public function addUser($arr)
    {
        return User::create([
            'fullName' => $arr['fullName'],
            'address'  => $arr['address'],
            'phone'    => $arr['phone'],
            'username' => $arr['username'],
            'password' => $arr['password'],
            'role_id'  => $arr['role_id'],
            'store_id' => $arr['store_id'],
        ]);

    }

    public function editUser(Request $request)
    {
        $this->validate($request, [
            'fullName' => "required",
            'username' => "required|unique:users,username,$request->id",
            'password' => "min:3",
            'role_id'  => "required",
            'store_id' => "required",
        ]);

        $data = [
            'fullName' => $request->fullName,
            'username' => $request->username,
            'role_id' => $request->role_id,
            'store_id' => $request->store_id
        ];

        if(!empty($request->password)){
            $password = bcrypt($request->password);
            $data["password"] = $password;
        }

        $user = User::where('id', $request->id)->update($data);

        return $this->getUser($request->id);
    }

    public function deleteUser($id)
    {
        // $this->validate($request, ['id' => "required",]);

        return User::where('id', $id)->delete();
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
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
