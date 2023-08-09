<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductsModel extends Controller
{
    /** ************************* Start CRUD *************************** **/
    public function addProduct($arr)
    {
        $product = Product::create($arr);

        return $this->getProduct($product->id);

    }

    public function update($id, $arr){
        Product::where("id", $id)->update($arr);

        return $this->getProduct($id);
    }

    public function delete($id)
    {
        return Product::where('id', $id)->delete();
    }


    public function getDepartmentProducts($department_id)
    {
        return DB::table("products as p")
            ->select("p.*", "f.name as fashion_name", "c.name as catetory_name")
            ->leftJoin("fashions as f", "f.id", "=", "p.fashion_id")
            ->leftJoin("product_categories as c", "c.id", "=", "p.category_id")
            ->where('p.department_id', $department_id)
            ->orderBy('p.name')
            ->orderBy('p.code')
            ->get();
    }

    public function getProduct($id)
    {
        return DB::table("products as p")
            ->select("p.*", "f.name as fashion_name", "c.name as catetory_name")
            ->leftJoin("fashions as f", "f.id", "=", "p.fashion_id")
            ->leftJoin("product_categories as c", "c.id", "=", "p.category_id")
            ->where('p.id', $id)
            ->first();
    }

    /** ************************* End CRUD *************************** **/

    //Xomashyo bulimining xomashyolari. Productlar doim kurinib turadi, faqat soni uzgaradi.
    public function getStockProducts($department_id, $category_id)
    {
        $productsCountQuery = DB::table("raw_material_details as rmd")
            ->select("rmd.product_id", DB::raw('SUM(rmd.count) as count'))
            ->where("rmd.department_id", $department_id)
            ->groupBy('rmd.product_id');

        return DB::table("products as p")
            ->select("p.*", "rmd.count")
            ->leftJoinSub($productsCountQuery, "rmd", function($join) {
                $join->on("p.id", "=", "rmd.product_id");
            })
            ->where("p.department_id", "=", $department_id)
            ->where("p.category_id", "=", $category_id)
            ->orderBy('p.name')
            ->orderBy('p.code')
            ->get();
    }

    /** ****************************************** REPORTS ************************************* **/
    public function getDepartmentRawMaterialsBalance($date, $department_id, $isRawMaterial)
    {
        $res = DB::select(
            DB::raw("SELECT p.id, p.`name`, p.measurement, p.code, p.country,
            rm.price, rm.price_value, rm.currency_id, rm.currency_rate, rm.count, rm.set_name, rm.inout, rm.created_at
            FROM products p
            LEFT JOIN (
                SELECT p.id as product_id, p.`name`, p.code, p.country, p.measurement
                , ROUND(AVG(rm.price), 0) AS price, ROUND(AVG(rm.price_value), 0) AS price_value, rm.currency_rate, rm.currency_id
                , SUM(rmd.count) AS `count`, rm.set_name, rmd.`inout`, rmd.created_at
                FROM products p
                LEFT JOIN raw_materials rm ON rm.product_id = p.id
                LEFT JOIN raw_material_details rmd ON rmd.raw_material_id = rm.id
                WHERE DATE(rmd.created_at) <= ? AND rmd.department_id = ? AND p.isRawMaterial = ?
                GROUP BY p.id
                ORDER BY p.`name` ASC
            ) rm ON rm.product_id = p.id
                 WHERE p.isRawMaterial = ?
            "), [$date, $department_id, $isRawMaterial, $isRawMaterial]
        );

        $raw_materials = array();
        foreach ($res as $product) {
            $raw_materials[$product->id] = $product;
        }

        return $raw_materials;
    }

    public function getDepartmentFinishedProductsBalance($date, $department_id, $product_department_id)
    {
        $res = DB::select(
            DB::raw("SELECT p.id, p.`name`, p.measurement, p.code, p.country, p.fashion_id,
            rm.price, rm.price_value, rm.currency_id, rm.currency_rate, rm.count, rm.set_name, rm.inout, rm.created_at,
            f.name as fashion_name
            FROM products p
            LEFT JOIN fashions f on f.id = p.fashion_id
            LEFT JOIN (
                SELECT p.id as product_id, p.`name`, p.code, p.country, p.measurement
                , ROUND(AVG(rm.price), 0) AS price, ROUND(AVG(rm.price_value), 0) AS price_value, rm.currency_rate, rm.currency_id
                , SUM(rmd.count) AS `count`, rm.set_name, rmd.`inout`, rmd.created_at
                FROM products p
                LEFT JOIN raw_materials rm ON rm.product_id = p.id
                LEFT JOIN raw_material_details rmd ON rmd.raw_material_id = rm.id
                WHERE DATE(rmd.created_at) <= ? AND rmd.department_id = ? AND p.isRawMaterial = 0 AND p.department_id = ?
                GROUP BY p.id
                ORDER BY p.`name` ASC
            ) rm ON rm.product_id = p.id
                 WHERE p.isRawMaterial = 0 AND p.department_id = ?
            "), [$date, $department_id, $product_department_id, $product_department_id]
        );

        $raw_materials = array();
        foreach ($res as $product) {
            $raw_materials[$product->id] = $product;
        }

        return $raw_materials;
    }

//    public function finishedProductRawMaterials($fashion_id, $start_date, $end_date)
//    {
//        return DB::table("products as p")
//            ->select("p2.name", "p.fashion_id", "rmd.finish_product_id", "p2.*",
//                DB::raw('AVG(rm.price) as price'),
//                DB::raw('SUM(rmd.count) as count'),
//            )
//            ->leftJoin("raw_material_details as rmd", "rmd.finish_product_id", "=", "p.id")
//            ->leftJoin("products as p2", "p2.id", "=", "rmd.product_id")
//            ->leftJoin("raw_materials as rm", "rm.id", "=", "rmd.raw_material_id")
//            ->where("p.fashion_id", $fashion_id)
//            ->where("p2.isRawMaterial", "=", 1)
//            ->where("rmd.created_at", ">=", $start_date)
//            ->where("rmd.created_at", "<=", $end_date)
//            ->groupBy("rmd.product_id")
//            ->orderBy("p2.name")
//            ->get();
//    }

}
