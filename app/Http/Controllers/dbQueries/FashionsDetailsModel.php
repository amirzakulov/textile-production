<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\FashionDetail;
use Illuminate\Support\Facades\DB;

class FashionsDetailsModel extends Controller
{
    public function getFashionDetails($fashion_id) {
        return DB::table("fashion_details as fd")
            ->select("fd.*", "p.name", "p.code", "p.country", "p.measurement", "p.color")
            ->leftJoin("products as p", "p.id", "=", "fd.product_id")
            ->where("fd.fashion_id", $fashion_id)
            ->get();
    }

    public function getFashionDetail($id) {
        return DB::table("fashion_details as fd")
            ->select("fd.*", "p.name", "p.code", "p.country", "p.measurement", "p.color")
            ->leftJoin("products as p", "p.id", "=", "fd.product_id")
            ->where("fd.id", $id)
            ->first();
    }

    public function add($arr) {
        $fashionDetail = FashionDetail::create($arr);
        return $this->getFashionDetail($fashionDetail->id);

    }

    public function update($id, $arr) {
        FashionDetail::where("id", $id)->update($arr);

        return $this->getFashionDetail($id);
    }

    public function delete($id) {
        return FashionDetail::where("id", $id)->delete();
    }
}
