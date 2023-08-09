<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunalTypesModel extends Controller
{
    public function getTypes(){
        return DB::table("communal_types as ct")->get();
    }

    public function getType($id){
        return DB::table("communal_types as ct")->where("ct.id", $id)->first();
    }



}
