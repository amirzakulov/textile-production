<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseTypesModel extends Controller
{
    public function getExpenseTypes(){
        return DB::table("expense_types as et")->get();
    }

    public function add($arr){
        return ExpenseType::create($arr);
    }

    public function update($id, $arr){
        return DB::table("expense_types as et")
        ->where('et.id', $id)
        ->update($arr);
    }

    public function delete($id){
        return ExpenseType::where('id', $id)->delete();
    }
}
