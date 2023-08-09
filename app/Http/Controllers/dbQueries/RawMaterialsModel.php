<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;

class RawMaterialsModel extends Controller
{

    public function getRawMaterial($id)
    {
        return DB::table("raw_materials as rm")
            ->select("rm.*", "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description")
            ->leftJoin("products as p", "p.id", "=", "rm.product_id")
            ->where("rm.id", $id)
            ->first();
    }

    public function add($arr)
    {
        $rm = RawMaterial::create($arr);

        return $this->getRawMaterial($rm->id);
    }

    public function update($id, $arr){
        RawMaterial::where("id", $id)->update($arr);

        return $this->getRawMaterial($id);
    }

    public function delete($id)
    {
        return RawMaterial::where('id', $id)->delete();
    }

    //Partiyaga tegishli barcha raw materiallani uchirish
    public function deleteBySet($set_id)
    {
        return RawMaterial::where('set_id', $set_id)->delete();
    }
}
