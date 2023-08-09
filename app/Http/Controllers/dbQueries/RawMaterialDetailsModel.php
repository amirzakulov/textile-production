<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialDetail;
use Illuminate\Support\Facades\DB;

class RawMaterialDetailsModel extends Controller
{

    /************************************
    * CRUD
     *************************************/
    public function getSetRawMaterials($set_id, $inout, $department_id) {
        return DB::table("raw_material_details as rmd")
            ->select("rm.id", "rm.price", "rm.price_value", "rm.currency_id", "rm.currency_rate",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
                "rmd.id as raw_material_detail_id", "rmd.count", "rmd.inout", "rmd.set_id", "rmd.from_set_id", "rmd.product_id", "rmd.department_id",
                "rmd.from_department_id", "rmd.to_department_id", "rmd.finish_raw_material_id", "rmd.finish_product_id",
                "c.name as currency_name", "s.name as set_name", "sf.name as from_set_name"
            )
            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmd.raw_material_id")
            ->leftJoin("products as p", "p.id", "=", "rm.product_id")
            ->leftJoin("currencies as c", "c.id", "=", "rm.currency_id")
            ->leftJoin("inout_sets as s", "s.id", "=", "rmd.set_id")
            ->leftJoin("inout_sets as sf", "sf.id", "=", "rmd.from_set_id")
            ->where("rmd.set_id", $set_id)
            ->where("rmd.inout", $inout)
            ->where("rmd.department_id", $department_id)
            ->get();
    }

    public function getRawMaterial($id)
    {
        return DB::table("raw_material_details as rmd")
            ->select("rm.price", "rm.price_value", "rm.currency_id", "rm.currency_rate",
                "rmd.id as raw_material_detail_id", "rmd.inout", "rmd.department_id", "rmd.from_department_id", "rmd.to_department_id", "rmd.product_id",  "rmd.set_id", "rmd.from_set_id", "rmd.count",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
                "c.name as currency_name"
            )
            ->leftJoin("products as p", "p.id", "=", "rmd.product_id")
            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmd.raw_material_id")
            ->leftJoin("currencies as c", "c.id", "=", "rm.currency_id")
            ->where("rmd.id", $id)
            ->first();
    }

    //Tayyor maxsulotni boshida xom ashyo omboriga kirim bulgandagi xolati
    public function getFinishedProductInitialData($department_id, $raw_material_id)
    {
        return DB::table("raw_material_details as rmd")
            ->select("rm.price", "rm.price_value", "rm.currency_id", "rm.currency_rate",
                "rmd.id as raw_material_detail_id", "rmd.raw_material_id", "rmd.inout", "rmd.department_id", "rmd.from_department_id",
                "rmd.to_department_id", "rmd.product_id",  "rmd.set_id", "rmd.from_set_id", "rmd.count",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
                "c.name as currency_name"
            )
            ->leftJoin("products as p", "p.id", "=", "rmd.product_id")
            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmd.raw_material_id")
            ->leftJoin("currencies as c", "c.id", "=", "rm.currency_id")
            ->where("rmd.department_id", $department_id)
            ->where("rmd.raw_material_id", $raw_material_id)
            ->where("rmd.inout", 1)
            ->first();
    }

    public function getFinishedProductRawMaterials($raw_material_id) {
        return DB::table("raw_material_details as rmd")
            ->select("rm.id", "rm.price", "rm.price_value", "rm.currency_id", "rm.currency_rate", "s.name as set_name",
                "p.name", "p.code", "p.color", "p.country", "p.measurement", "p.picture", "p.description",
                "rmd.id as raw_material_detail_id", "rmd.count", "rmd.inout", "rmd.set_id", "rmd.from_set_id", "rmd.product_id", "rmd.department_id",
                "rmd.finish_raw_material_id", "rmd.finish_product_id",
                "c.name as currency_name"
            )
            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmd.raw_material_id")
            ->leftJoin("inout_sets as s", "s.id", "=", "rmd.set_id")
            ->leftJoin("products as p", "p.id", "=", "rm.product_id")
            ->leftJoin("currencies as c", "c.id", "=", "rm.currency_id")
            ->where("rmd.finish_raw_material_id", $raw_material_id)
            ->get();
    }

    public function getFinishedProductRawMaterialsByProduct($product_id, $start_date, $end_date) {

        $query = DB::select(
                    DB::raw("
                        SELECT  rmd.name, rmd.measurement, rmd.isRawMaterial, rmd.raw_material_id, rmd.product_id, rmd.finish_raw_material_id,
                        AVG(rmd.price) as avg_price, SUM(rmd.count) as `count`
                        FROM    (
                         SELECT  p.name, p.measurement, p.isRawMaterial, rm.price, rmd.count,
                         rmd.raw_material_id, rmd.product_id, rmd.finish_raw_material_id
                         FROM  raw_material_details rmd
                         LEFT JOIN products p ON p.id = rmd.product_id
                         LEFT JOIN raw_materials rm ON rm.id = rmd.raw_material_id
                         WHERE DATE(rmd.created_date) >= ? AND DATE(rmd.created_date) <= ?
                         ORDER BY rmd.finish_raw_material_id, rmd.raw_material_id
                        ) rmd, (select @pv := ?) initialisation
                        WHERE find_in_set(rmd.finish_raw_material_id, @pv) IS NOT NULL
                        AND   find_in_set(rmd.isRawMaterial, 1)
                        AND @pv := concat(@pv, ',', rmd.product_id)
                        GROUP BY rmd.product_id
                    "), [$start_date, $end_date, $product_id]
            );

        return $query;


    }

    public function add($arr)
    {
        $rmd = RawMaterialDetail::create($arr);

        return $this->getRawMaterial($rmd->id);
    }

    public function update($id, $arr){
        RawMaterialDetail::where("id", $id)->update($arr);

        return $this->getRawMaterial($id);
    }

    public function updateByDepartmentId($raw_material_id, $department_id, $inout, $set_id, $arr){
        RawMaterialDetail::where("raw_material_id", $raw_material_id)
            ->where("department_id", $department_id)
            ->where("inout", $inout)
            ->where("set_id", $set_id)
            ->update($arr);

        return $this->getRawMaterial($raw_material_id);
    }

    public function updateBySetId($department_id, $set_id, $raw_material_id, $arr){
        return RawMaterialDetail::where("department_id", $department_id)
            ->where("set_id", $set_id)
            ->where("raw_material_id", $raw_material_id)
            ->update($arr);
    }

    public function delete($id)
    {
        return RawMaterialDetail::where('id', $id)->delete();
    }

    public function deleteBySet($set_id)
    {
        return RawMaterialDetail::where('set_id', $set_id)->delete();
    }

    public function deleteInOut($raw_material_id, $set_id)
    {
        return RawMaterialDetail::where('raw_material_id', $raw_material_id)
            ->where('set_id', $set_id)
            ->delete();
    }

    /** ************************************ END CRUD **************************************** **/


    /** ************************************ START FUNCTIONS **************************************** **/

    //malum Raw Materialni malum bulimdagi soni
    public function getRawMaterialCount($raw_material_id, $department_id, $set_id)
    {
        return DB::table("raw_material_details as rmd")
            ->where("rmd.raw_material_id", $raw_material_id)
            ->where("rmd.department_id", $department_id)
            ->where("rmd.set_id", $set_id)
            ->groupBy("rmd.raw_material_id")
            ->groupBy("rmd.department_id")
            ->groupBy("rmd.set_id")
            ->sum('rmd.count');
    }

    /** ************************************ END FUNCTIONS **************************************** **/


    /** ************************************  **************************************** **/
    public function getInOutProductsByDate($inout, $startDate, $endDate, $department_id, $isRawMaterial)
    {
        return DB::table("raw_material_details as rmd")
            ->selectRaw("rmd.product_id, SUM(rmd.count) as count, AVG(rm.price) as price, AVG(rm.price_value) as price_value, rm.currency_rate,
            p.name, p.code, p.measurement, f.name as fashion_name, f.code as fashion_code")
            ->leftJoin("raw_materials as rm", "rm.id","=","rmd.raw_material_id")
            ->leftJoin("products as p", "p.id","=","rmd.product_id")
            ->leftJoin("fashions as f", "f.id","=","p.fashion_id")
            ->whereRaw("DATE(rmd.created_date) >= ?", [$startDate])
            ->whereRaw("DATE(rmd.created_date) <= ?", [$endDate])
            ->where("rmd.inout", $inout)
            ->where("rmd.department_id", $department_id)
            ->where("p.isRawMaterial", $isRawMaterial)
            ->groupBy("rmd.product_id")
            ->get();
    }

    public function getDepartmentSets($department_id, $isRawMaterial, $inout = null, $department_products = false){
        $query = DB::table("raw_material_details as rmd")
            ->select("rmd.inout", "rmd.set_id", "rmd.from_set_id", "rmd.from_department_id", "rmd.to_department_id", "s.name",
                "s.created_date", "s.created_at", "u.fullName", "df.name as department_from", "dt.name as department_to", "s.department_id")
            ->leftJoin("inout_sets as s", "s.id", "=", "rmd.set_id")
            ->leftJoin("users as u", "u.id", "=", "s.user_id")
            ->leftJoin("departments as df", "df.id", "=", "rmd.from_department_id")
            ->leftJoin("departments as dt", "dt.id", "=", "rmd.to_department_id")
            ->leftJoin("products as p", "p.id", "=", "rmd.product_id")
            ->where("rmd.department_id", $department_id);

        if($department_products) {
            $query = $query->where("p.department_id", $department_id);
        } else {
            $query = $query->where("p.department_id", "!=", $department_id);
        }

        if(!is_null($inout)) {
            $query = $query->where("rmd.inout", $inout);
        }

        $query = $query->where("s.isRawMaterial", $isRawMaterial)
            ->groupBy("rmd.set_id")
            ->orderBy("s.created_date", 'desc')
            ->get();

        return $query;
    }

    public function getDepartmentSet($department_id, $set_id){
        return DB::table("raw_material_details as rmd")
            ->select("rmd.inout", "rmd.set_id", "rmd.from_department_id", "rmd.to_department_id", "rmd.department_id",
                "s.name", "s.user_id", "s.isRawMaterial", "s.created_date", "s.created_at",
                "u.fullName", "df.name as department_from", "dt.name as department_to")
            ->leftJoin("inout_sets as s", "s.id", "=", "rmd.set_id")
            ->leftJoin("users as u", "u.id", "=", "s.user_id")
            ->leftJoin("departments as df", "df.id", "=", "rmd.from_department_id")
            ->leftJoin("departments as dt", "dt.id", "=", "rmd.to_department_id")
            ->where("rmd.department_id", $department_id)
            ->where("rmd.set_id", $set_id)
            ->groupBy("rmd.set_id")
            ->first();
    }

    //Kirim yoki Chiqim qilingan partiyadagi raw material ishlatildimi yuqmi tekshiradi
    public function isRawMaterialUsed($raw_material_id, $department_id, $set_id)
    {
        $rmCount = DB::table("raw_material_details as rmd")
            ->where("rmd.raw_material_id", $raw_material_id)
            ->where("rmd.inout", 2)
            ->where("rmd.department_id", "=", $department_id)
            ->where("rmd.from_set_id", "=", $set_id)
            ->count();

        if($rmCount > 0)
            return true;
        else
            return false;
    }

    //Raw Materiallari Chiqim qilingan partiyadagi tayyor maxsulot ishlatildimi yuqmi tekshiradi
    //department_id bu tayyor maxsulot yaratilgan bulim id si
    public function isFinishProductUsed($raw_material_id, $department_id, $set_id)
    {
        $rmCount = DB::table("raw_material_details as rmd")
            ->where("rmd.raw_material_id", $raw_material_id)
            ->where("rmd.inout", 2)
            ->where("rmd.department_id", "=", $department_id)
            ->where("rmd.from_set_id", "=", $set_id)
            ->count();

        if($rmCount > 0)
            return true;
        else
            return false;
    }

}
