<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\Fashion;
use App\Models\FashionDetail;
use Illuminate\Support\Facades\DB;

class FashionsModel extends Controller
{
    public function getFashions(){
        return DB::table("fashions as f")
            ->select("f.*", "c.name as category_name")
            ->leftJoin("fashion_categories as c", "c.id", "=", "f.fashion_category_id")
            ->get();
    }

    public function getFashion($id){
        return DB::table("fashions as f")
            ->select("f.*", "c.name as category_name")
            ->leftJoin("fashion_categories as c", "c.id", "=", "f.fashion_category_id")
            ->where("f.id", $id)
            ->first();
    }

    public function addFashion($arr){
        $fashion = Fashion::create($arr);

        return $this->getFashion($fashion->id);
    }

    public function editFashion($id, $arr){
        Fashion::where("id", $id)->update($arr);

        return $this->getFashion($id);
    }

    public function deleteFashion($id){
        FashionDetail::where('fashion_id', $id)->delete();
        return Fashion::where('id', $id)->delete();
    }
}
