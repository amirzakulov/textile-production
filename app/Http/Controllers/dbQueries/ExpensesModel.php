<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpensesModel extends Controller
{
    public function getExpenses(){
        return DB::table("expenses as e")
        ->select("e.*", "et.name")
        ->leftJoin("expense_types as et", "et.id", "=", "e.expense_type_id")
        ->orderBy("e.date", "desc")
        ->orderBy("e.created_at", "desc")
        ->get();
    }

    public function getExpense($id){
        return DB::table("expenses as e")
        ->select("e.*", "et.name")
        ->leftJoin("expense_types as et", "et.id", "=", "e.expense_type_id")
        ->where("e.id", $id)
        ->first();
    }

    public function add($arr){
        return Expense::create($arr);
    }

    public function update($id, $arr){
        return DB::table("expenses as e")
        ->where('e.id', $id)
        ->update($arr);
    }

    public function delete($id){
        return Expense::where('id', $id)->delete();
    }

    public function getMonthlyExpenses($year, $month){

        return DB::table("expenses as e")
        ->select("e.*")
        ->whereRaw('MONTH(e.date) = ?',[$month])
        ->whereRaw('YEAR(e.date) = ?',[$year])
        ->get();
    }

    public function expensesByType($startDate, $endDate){
        return DB::table("expenses as e")
            ->select("et.name", "e.expense_type_id", DB::raw('SUM(e.amount) as amount'),
            )
            ->leftJoin("expense_types as et", "et.id", "=", "e.expense_type_id")
            ->where("e.date", ">=", $startDate)
            ->where("e.date", "<=", $endDate)
            ->groupBy("e.expense_type_id")
            ->orderBy("et.name")
            ->get();
    }
}
