<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class CurrencyModel extends Controller
{
    public function getCurrencies(){
        return DB::table("currencies as c")->get();
    }
    public function getCurrency($id)
    {
        return DB::table("currencies")
            ->where("id", $id)
            ->first();
    }
}
