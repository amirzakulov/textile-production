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
        ->where("r.isAdmin", 1)
        ->orderBy("u.fullName")
        ->get();
    }

    public function getUser($user_id)
    {
        return DB::table("users as u")
        ->select("u.id", "u.fullName", "u.username", "u.role_id", "u.phone", "u.address", "r.roleName")
        ->leftJoin("roles as r", "r.id", "=", "u.role_id")
        ->where("u.id", $user_id)
        ->first();
    }

    public function addUser($arr)
    {
        $user = User::create($arr);

        return $this->getUser($user->id);

    }

    public function update($id, $arr)
    {
        User::where('id', $id)->update($arr);

        return $this->getUser($id);
    }

    public function delete($id)
    {
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
