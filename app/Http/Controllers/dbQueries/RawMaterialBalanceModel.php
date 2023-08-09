<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialBalance;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Collection;

class RawMaterialBalanceModel extends Controller
{
    public function getRawMaterial($id)
    {
        return DB::table("raw_material_balances as rmb")
            ->select("rmb.*",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
            )
            ->leftJoin("products as p", "p.id", "=", "rmb.product_id")
            ->where("rmb.id", $id)
            ->first();
    }

    public function getRawMaterialBySetid($raw_material_id, $department_id, $set_id)
    {
        return DB::table("raw_material_balances as rmb")
            ->select("rmb.*",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
            )
            ->leftJoin("products as p", "p.id", "=", "rmb.product_id")
            ->where("rmb.raw_material_id", $raw_material_id)
            ->where("rmb.department_id", $department_id)
            ->where("rmb.set_id", $set_id)
            ->first();
    }

    public function getRawMaterialByID($id)
    {
        return DB::table("raw_material_balances as rmb")
            ->select("rmb.*",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
            )
            ->leftJoin("products as p", "p.id", "=", "rmb.product_id")
            ->where("rmb.id", $id)
            ->first();
    }

    public function add($arr)
    {
        $rmb = RawMaterialBalance::create($arr);

        return $this->getRawMaterialByID($rmb->id);
    }

    public function update($id, $arr)
    {
        RawMaterialBalance::where("id", $id)->update($arr);

        return $this->getRawMaterial($id);
    }

    public function updateBySetid($raw_material_id, $department_id, $set_id, $arr)
    {
        RawMaterialBalance::where("raw_material_id", $raw_material_id)
            ->where("department_id", $department_id)
            ->where("set_id", $set_id)
            ->update($arr);

        return $this->getRawMaterialBySetid($raw_material_id, $department_id, $set_id);
    }

    //malum rawMaterial ni uchirish
    public function delete($id)
    {
        return RawMaterialBalance::where('id', $id)->delete();
    }

    public function deleteBySetid($raw_material_id, $department_id, $set_id)
    {
        return RawMaterialBalance::where('raw_material_id', $raw_material_id)
            ->where('department_id', $department_id)
            ->where('set_id', $set_id)
            ->delete();

    }

    //malum rawMaterial ni barcha blimdan uchirish
    public function deleteByRmID($raw_material_id){
        return RawMaterialBalance::where('raw_material_id', $raw_material_id)->delete();
    }

    /** ******************************END CRUD ********************************** **/

    /**
     * Bulimning xomashyolari raw_material_id buyicha.
     *
     * bulimning ID si
     * @param  int  $department_id
     *
     * isRawMaterial = null: raw material va finished Products;
     * isRawMaterial = 1:    raw material;
     * isRawMaterial = 0:    finished Products
     * @param  int  $isRawMaterial
     *
     * @return Collection
     */
    public function getDepartmentRawMaterials($department_id, $isRawMaterial = null, $department_products = false)
    {
        $query = DB::table("raw_material_balances as rmb")
            ->select("rmb.*",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
                "s.name as set_name"
            )
            ->leftJoin("products as p", "p.id", "=", "rmb.product_id")
            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmb.raw_material_id")
            ->leftJoin("inout_sets as s", "s.id", "=", "rmb.set_id")
            ->where("rmb.department_id", $department_id);

        if(!is_null($isRawMaterial)){
            $query = $query->where("p.isRawMaterial", $isRawMaterial);
        }

        if($department_products) {
            $query = $query->where("p.department_id", $department_id);
        } else {
            $query = $query->where("p.department_id", "!=", $department_id);
        }

        $query = $query->orderBy("p.name")
            ->orderBy("p.code")
            ->get();

        return $query;
    }

    /**
     * Bulimning xomashyolari product_id buyicha.
     *
     * @param  int  $department_id
     *
     * isRawMaterial = 1: raw material;
     * isRawMaterial = 0: Finished Products
     * @param  int  $isRawMaterial
     *
     * agar true bulsa, bulimning uziga tegishli maxsulotlar
     * @param  boolean  $department_products
     *
     * @return array
     */
    public function getDepartmentStockProducts($department_id, $isRawMaterial, $department_products = false)
    {
        $query = DB::table("raw_material_balances as rmb")
            ->select("rmb.product_id", "rmb.department_id", DB::raw("SUM(rmb.count) AS `count`"),
                DB::raw("AVG(rmb.price) AS `price`"),
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
                "rm.set_name"
            )
            ->leftJoin("products as p", "p.id", "=", "rmb.product_id")
            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmb.raw_material_id")
            ->where("rmb.department_id", $department_id);

            if($department_products) {
                $query = $query->where("p.department_id", $department_id);
            } else {
                $query = $query->where("p.department_id", "!=", $department_id);
            }

        $query = $query->where("p.isRawMaterial", $isRawMaterial)
            ->groupBy("rmb.product_id")
            ->orderBy("p.name")
            ->orderBy("p.code")
            ->get();

        return $query;
    }
}
