<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\CommunalExpense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunalExpensesModel extends Controller
{
    public function getExpenses($communal_type_id, $device_id = null){

        return DB::table("communal_expenses as ce")
        ->select("ce.*", "cd.name as device", "ct.name as communal_type")
        ->leftJoin("communal_devices as cd", "cd.id", "=", "ce.device_id")
        ->leftJoin("communal_types as ct", "ct.id", "=", "ce.communal_type_id")
        ->where("ce.communal_type_id", $communal_type_id)
        ->orderBy("ce.date", "desc")
        ->orderBy("ce.created_at", "desc")
        ->get();
    }

    public function getPayment($id){

        return DB::table("communal_expenses as ce")
        ->select("ce.*", "cd.name as device", "ct.name as communal_type")
        ->leftJoin("communal_devices as cd", "cd.id", "=", "ce.device_id")
        ->leftJoin("communal_types as ct", "ct.id", "=", "ce.communal_type_id")
        ->where("ce.id", $id)
        ->first();
    }

    public function getPreviousePayment($id, $communal_type_id, $device_id){

        return DB::table("communal_expenses as ce")
        ->select("ce.*", "cd.name as device", "ct.name as communal_type")
        ->leftJoin("communal_devices as cd", "cd.id", "=", "ce.device_id")
        ->leftJoin("communal_types as ct", "ct.id", "=", "ce.communal_type_id")
        ->where("ce.id", '<', $id)
        ->where("ce.communal_type_id", $communal_type_id)
        ->where("ce.device_id", $device_id)
        ->orderBy('id','desc')
        ->first();
    }

    public function getNextPayment($id, $communal_type_id, $device_id){

        return DB::table("communal_expenses as ce")
        ->select("ce.*", "cd.name as device", "ct.name as communal_type")
        ->leftJoin("communal_devices as cd", "cd.id", "=", "ce.device_id")
        ->leftJoin("communal_types as ct", "ct.id", "=", "ce.communal_type_id")
        ->where("ce.id", '>', $id)
        ->where("ce.communal_type_id", $communal_type_id)
        ->where("ce.device_id", $device_id)
        ->orderBy('id','desc')
        ->first();
    }

    public function addPayment($arr){
        return CommunalExpense::create($arr);
    }

    public function update($id, $arr){
        return DB::table("communal_expenses as ce")
        ->where('ce.id', $id)
        ->update($arr);
    }

    public function deletePayment($id){

        return CommunalExpense::where('id', $id)->delete();
    }

    public function lastPayment($communal_type_id, $device_id){
        return DB::table("communal_expenses as ce")
        ->select("ce.*")
        ->where("ce.communal_type_id", $communal_type_id)
        ->where("ce.device_id", $device_id)
        ->orderBy("ce.date", "desc")
        ->first();
    }

    public function communalReport($startDate, $endDate){

        return DB::table("communal_expenses as ce")
        ->select("ce.communal_type_id", "ct.name", "ct.measurement",
            DB::raw('SUM(ce.active_amount) as active_amount'),
            DB::raw('SUM(ce.reactive_amount) as reactive_amount'),
            DB::raw('SUM(ce.payment) as payment')
        )
        ->leftJoin("communal_types as ct", "ct.id", "=", "ce.communal_type_id")
        ->where("ce.date", ">=", $startDate)
        ->where("ce.date", "<=", $endDate)
        ->groupBy("ce.communal_type_id")
        ->get();
    }

    public function getMonthlyExpense($year, $month){

        return DB::table("communal_expenses as ce")
        ->select("ce.*")
        ->whereRaw('MONTH(ce.date) = ?',[$month])
        ->whereRaw('YEAR(ce.date) = ?',[$year])
        ->get();
    }
}
