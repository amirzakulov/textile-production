<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\CommunalDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunalDevicesModel extends Controller
{
    public function getDevice($id){
        return DB::table("communal_devices as cd")
        ->select("cd.*", "ct.measurement")
        ->leftJoin("communal_types as ct", "ct.id", "=", "cd.communal_type_id")
        ->where("cd.id", $id)
        ->first();
    }

    public function getDevices($communal_type_id){
        return DB::table("communal_devices as cd")
        ->select("cd.*", "ct.measurement")
        ->leftJoin("communal_types as ct", "ct.id", "=", "cd.communal_type_id")
        ->where("communal_type_id", $communal_type_id)->get();
    }

    public function addDevice($arr){
        return CommunalDevice::create($arr);
    }

    public function update($id, $arr){
        return DB::table("communal_devices as cd")
        ->where('cd.id', $id)
        ->update($arr);
    }

    public function deleteDevice($id){
        return CommunalDevice::where('id', $id)->delete();
    }
}
