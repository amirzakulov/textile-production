<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\InOutSetsModel;
use App\Http\Controllers\dbQueries\ProductsModel;
use App\Http\Controllers\dbQueries\RawMaterialsModel;
use App\Http\Controllers\dbQueries\RawMaterialDetailsModel;
use App\Http\Controllers\dbQueries\RawMaterialBalanceModel;
use App\Http\Controllers\dbQueries\DepartmentsModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinishedProductsController extends Controller
{
    private $inoutSetsModel;
    private $rawMaterialsModel;
    private $rawMaterialDetailsModel;
    private $rawMaterialBalanceModel;
    private $departmentsModel;
    private $productsModel;
    private $user;

    public function __construct(){
        $this->inoutSetsModel           = new InOutSetsModel();
        $this->rawMaterialsModel        = new RawMaterialsModel();
        $this->rawMaterialDetailsModel  = new RawMaterialDetailsModel();
        $this->rawMaterialBalanceModel  = new RawMaterialBalanceModel();
        $this->departmentsModel         = new DepartmentsModel();
        $this->productsModel            = new ProductsModel();
        $this->user                     = Auth::user();
    }

    /** ****************************** START ACTIONS ********************************** **/


    /** ****************************** END ACTIONS ********************************** **/

    //Bulimning Tayyor maxsulotlari jami soni bilan
    public function getDepartmentFinishProductsStock($department_id) {
        return $this->rawMaterialBalanceModel->getDepartmentStockProducts($department_id, 0);
    }

    public function getDepartmentFinishProductsSets($department_id)
    {
        $isRawMaterial = 0;
        return $this->rawMaterialDetailsModel->getDepartmentSets($department_id, $isRawMaterial);
    }

    public function getFinishProducts($department_id)
    {
        $isRawMaterial = 0;             //tayyor maxsulot
        $department_products = false;   //tayyor maxsulotlar omborida bulimga tegishli bulmagan tayyor maxsulotlarni kursatsin

        return $this->rawMaterialBalanceModel->getDepartmentRawMaterials($department_id, $isRawMaterial, $department_products);
    }

    public function addOutgoingFinishProducts(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $products       = $request->products;
        $department_id  = $request->department_id;
        $created_date   = dateToDateTime($request->created_date);

        //step 1 : Partiya yaratamiz
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');
        $inoutSetArr = [
            'name'              => $inoutSetName,
            "user_id"           => $this->user->id,
            'inout'             => 2,
            'from_department_id'=> $inoutSet["from_department_id"],
            'to_department_id'  => $inoutSet["to_department_id"],
            'department_id'     => $department_id,
            'isRawMaterial'     => 0,
            'created_date'      => $created_date,
        ];

        $set = $this->inoutSetsModel->add($inoutSetArr);

        //step 2 : create Raw Material Details
        foreach ($products as $product) {
//            $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $department_id);
            $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"]);

            //chiqim qilamiz
            $rmdArr = [
                "raw_material_id"   => $rawMaterial->raw_material_id,
                "inout"             => 2,
                "department_id"     => $department_id,
                "product_id"        => $rawMaterial->product_id,
                "set_id"            => $set->id,
                "from_set_id"       => $rawMaterial->set_id,
                "count"             => (-1) * $product["count"],
                "user_id"           => $this->user->id,
                "from_department_id"=> $inoutSet["from_department_id"],
                "to_department_id"  => $inoutSet["to_department_id"],
                'created_date'      => $created_date,
            ];

            $this->rawMaterialDetailsModel->add($rmdArr);

            //kirim qilamiz
            $rmdArr = [
                "raw_material_id"   => $rawMaterial->raw_material_id,
                "inout"             => 1,
                "department_id"     => $inoutSet["to_department_id"],
                "product_id"        => $rawMaterial->product_id,
                "set_id"            => $set->id,
                "from_set_id"       => $rawMaterial->set_id,
                "count"             => $product["count"],
                "user_id"           => $this->user->id,
                "from_department_id"=> $inoutSet["from_department_id"],
                "to_department_id"  => $inoutSet["to_department_id"],
                'created_date'      => $created_date,
            ];

            $this->rawMaterialDetailsModel->add($rmdArr);

            //step 3 : create Raw Material Balance
            //chiqim qilamiz
            $count = $rawMaterial->count - $product["count"];
            if($count > 0){
                $rmBalanceArr   = ["count" => $count];
                $this->rawMaterialBalanceModel->update($product["raw_material_balance_id"], $rmBalanceArr);
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $rmBalanceArr);
            } else {
                $this->rawMaterialBalanceModel->delete($product["raw_material_balance_id"]);
//                $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id);
            }

            //kirim qilamiz
            $rmBalanceArr = [
                "raw_material_id"   => $rawMaterial->raw_material_id,
                "product_id"        => $rawMaterial->product_id,
                "count"             => $product["count"],
                "price"             => $rawMaterial->price,
                "department_id"     => $inoutSet["to_department_id"],
                "set_id"            => $set->id,
            ];

            $this->rawMaterialBalanceModel->add($rmBalanceArr);
//            $rawMaterialIn = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $inoutSet["to_department_id"]);
//            if(!empty($rawMaterialIn)){
//                $count          = $rawMaterial->count + $product["count"];
//                $rmBalanceArr   = ["count" => $count];
//
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $inoutSet["to_department_id"], $rmBalanceArr);
//            } else {
//                $rmBalanceArr = [
//                    "raw_material_id"   => $product["raw_material_id"],
//                    "product_id"        => $rawMaterial->product_id,
//                    "count"             => $product["count"],
//                    "price"             => $rawMaterial->price,
//                    "department_id"     => $inoutSet["to_department_id"],
//                ];
//
//                $this->rawMaterialBalanceModel->add($rmBalanceArr);
//            }
        }

        $isRawMaterial = 0;
        return $this->rawMaterialDetailsModel->getDepartmentSets($department_id, $isRawMaterial);
    }

    public function deleteFinishProductsOutSet(Request $request)
    {
        $set_id             = $request->set_id;
        $department_id      = $request->department_id;
        $to_department_id   = $request->to_department_id;

        $finishedProducts = $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $request->inout, $department_id);
        foreach ($finishedProducts as $finishProduct) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($finishProduct->id, $to_department_id, $set_id)) return response()->json(false);
        }

        //Step - 1 : raw materiallani RawMaterialDetails tabledan uchiramiz
        $this->rawMaterialDetailsModel->deleteBySet($set_id);
        foreach ($finishedProducts as $rawMaterial) {

            //Step 2: Raw Material Balance table dagi chiqim qilingan bulimning rawMaterialni malum bulimda update qilamiz
            $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $department_id, $rawMaterial->from_set_id);

//            $rawMaterialBalanceOut = $this->rawMaterialBalanceModel->getRawMaterial($rawMaterial->id, $department_id);
            $rawMaterialBalanceOut = $this->rawMaterialBalanceModel->getRawMaterialBySetid($rawMaterial->id, $department_id, $rawMaterial->from_set_id);
            if(!is_null($rawMaterialBalanceOut)){
                $rmbArr = [
                    'count' => $rmdCountOut,
                ];
//                $this->rawMaterialBalanceModel->update($rawMaterial->id, $department_id, $rmbArr);
                $this->rawMaterialBalanceModel->update($rawMaterialBalanceOut->id, $rmbArr);
            } else {
                $rmBalanceArr = [
                    "raw_material_id"   => $rawMaterial->id,
                    "product_id"        => $rawMaterial->product_id,
                    "count"             => $rmdCountOut,
                    "price"             => $rawMaterial->price,
                    "department_id"     => $department_id,
                    "set_id"            => $rawMaterial->from_set_id,
                ];

                $this->rawMaterialBalanceModel->add($rmBalanceArr);
            }

            //Step 3: Raw Material Balance table dagi kirim qilingan bulimning rawMaterialni malum bulimda update qilamiz
            $this->rawMaterialBalanceModel->deleteBySetid($rawMaterial->id, $to_department_id, $set_id);
//            $rmdCountIn = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $to_department_id);
//            if($rmdCountIn > 0){
//                $rmbArr = [
//                    'count' => $rmdCountIn,
//                ];
//                $this->rawMaterialBalanceModel->update($rawMaterial->id, $to_department_id, $rmbArr);
//            } else {
//                $this->rawMaterialBalanceModel->delete($rawMaterial->id, $to_department_id);
//            }
        }

        //Partiyani uchiramiz inout_sets tabledan
        return $this->inoutSetsModel->delete($set_id);

    }

    public function finishedProductRawMaterials(Request $request) {

        $year          = strval($request->year);
        $month         = strval($request->month);
        $product_id    = $request->product_id;
        $department_id = $request->department_id;

        $dateString = $year.'-'.$month.'-01';

        $date = new DateTime($dateString);
        $date->modify('first day of this month');
        $first_day_this_month = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $last_day_this_month = $date->format('Y-m-d');

        return $this->rawMaterialDetailsModel->getFinishedProductRawMaterialsByProduct($product_id, $first_day_this_month, $last_day_this_month);
    }

    public function addOutgoingProduct(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $product        = $request->rawMaterial;
        $department_id  = $request->department_id;
        $set_id         = $inoutSet["id"];

        $set            = $this->inoutSetsModel->getSet($set_id);
        $created_date   = $set->created_date;

        //step 1 : create Raw Material Details
//        $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $department_id);
        $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"]);
        //chiqim qilamiz
        $rmdArr = [
            "raw_material_id"   => $rawMaterial->raw_material_id,
            "inout"             => 2,
            "department_id"     => $department_id,
            "product_id"        => $rawMaterial->product_id,
            "set_id"            => $set_id,
            "from_set_id"       => $rawMaterial->set_id,
            "count"             => (-1) * $product["count"],
            "user_id"           => $this->user->id,
            "from_department_id"=> $inoutSet["from_department_id"],
            "to_department_id"  => $inoutSet["to_department_id"],
            "created_date"      => $created_date,
        ];

        $this->rawMaterialDetailsModel->add($rmdArr);

        //kirim qilamiz
        $rmdArr = [
            "raw_material_id"   => $product["raw_material_id"],
            "inout"             => 1,
            "department_id"     => $inoutSet["to_department_id"],
            "product_id"        => $rawMaterial->product_id,
            "set_id"            => $set_id,
            "from_set_id"       => $rawMaterial->set_id,
            "count"             => $product["count"],
            "user_id"           => $this->user->id,
            "from_department_id"=> $inoutSet["from_department_id"],
            "to_department_id"  => $inoutSet["to_department_id"],
            "created_date"      => $created_date,
        ];

        $this->rawMaterialDetailsModel->add($rmdArr);

        //step 2 : create Raw Material Balance
        //chiqim qilamiz
        $count = $rawMaterial->count - $product["count"];
        if($count > 0){
            $rmBalanceArr   = ["count" => $count];

//            $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $rmBalanceArr);
            $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->raw_material_id, $department_id, $rawMaterial->set_id, $rmBalanceArr);
        } else {
//            $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id);
            $this->rawMaterialBalanceModel->deleteBySetid($rawMaterial->raw_material_id, $department_id, $rawMaterial->set_id);
        }

        //kirim qilamiz
        $rmBalanceArr = [
            "raw_material_id"   => $rawMaterial->raw_material_id,
            "product_id"        => $rawMaterial->product_id,
            "count"             => $product["count"],
            "price"             => $rawMaterial->price,
            "department_id"     => $inoutSet["to_department_id"],
            "set_id"            => $inoutSet["id"],
        ];

        $this->rawMaterialBalanceModel->add($rmBalanceArr);
//        $rawMaterialIn = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $inoutSet["to_department_id"]);
//        if(!empty($rawMaterialIn)){
//            $count          = $rawMaterial->count + $product["count"];
//            $rmBalanceArr   = ["count" => $count];
//
//            $this->rawMaterialBalanceModel->update($product["raw_material_id"], $inoutSet["to_department_id"], $rmBalanceArr);
//        } else {
//            $rmBalanceArr = [
//                "raw_material_id"   => $product["raw_material_id"],
//                "product_id"        => $rawMaterial->product_id,
//                "count"             => $product["count"],
//                "price"             => $rawMaterial->price,
//                "department_id"     => $inoutSet["to_department_id"],
//            ];
//
//            $this->rawMaterialBalanceModel->add($rmBalanceArr);
//        }

        return $this->rawMaterialDetailsModel->getSetRawMaterials($inoutSet["set_id"], 2, $department_id);
    }

    public function editOutgoingProduct(Request $request)
    {
        $raw_material_id    = $request->raw_material_id;
        $count              = $request->count;
        $stockCount         = $request->stockCount + $request->outCount;
        $department_id      = $request->department_id;
        $from_department_id = $request->from_department_id;
        $to_department_id   = $request->to_department_id;
        $set_id             = $request->set_id;
        $from_set_id        = $request->from_set_id;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($raw_material_id, $to_department_id, $set_id)) return response()->json(false);

        //Step 1: Raw Material Details ni update qilamiz
        $rmdOutArr = ["count" => (-1) * $count];
        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($raw_material_id, $from_department_id, 2, $set_id, $rmdOutArr);

        $rmdInArr = ["count" => $count];
        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($raw_material_id, $to_department_id, 1, $set_id, $rmdInArr);

        //Step 4: Raw Material Balance ni update qilamiz
        $rmbCount = $stockCount - $count;
        $rmbOutArr = ['count' => $rmbCount];
        if($rmbCount === 0) {
//            $this->rawMaterialBalanceModel->delete($raw_material_id, $from_department_id);
            $this->rawMaterialBalanceModel->deleteBySetid($raw_material_id, $from_department_id, $from_set_id);
        } elseif ($request->stockCount === 0) {
            $rmBalanceArr = [
                "raw_material_id"   => $raw_material_id,
                "product_id"        => $request->product_id,
                "count"             => $rmbCount,
                "price"             => $request->price,
                "department_id"     => $department_id,
                "set_id"            => $from_set_id,
            ];
            $this->rawMaterialBalanceModel->add($rmBalanceArr);
        } else {
//            $this->rawMaterialBalanceModel->update($raw_material_id, $from_department_id, $rmbOutArr);
            $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $from_department_id, $from_set_id, $rmbOutArr);
        }

        $rmbInArr = ['count' => $count];
//        $this->rawMaterialBalanceModel->update($raw_material_id, $to_department_id, $rmbInArr);
        $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $to_department_id, $set_id, $rmbInArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $request->inout, $department_id);
    }

    public function deleteOutgoingProduct(Request $request)
    {
        $this->validate($request, [
            'id'            => "required",
            'set_id'        => "required",
            'department_id' => "required",
        ]);

        $raw_material_id  = $request->id;
        $department_id    = $request->department_id;
        $set_id           = $request->set_id;
        $to_department_id = $request->to_department_id;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($raw_material_id, $to_department_id, $set_id)) return response()->json(false);

        //Step 1: Raw Material Details table dan rawMaterialni malum partiyadan chiqim va kirimini delete qilamiz
        $this->rawMaterialDetailsModel->deleteInOut($raw_material_id, $set_id);

        //Step 2: Raw Material Balance table dagi chiqim qilingan rawMaterialni malum bulimda update qilamiz
//        $rmdCountOut    = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id);
//        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterial($raw_material_id, $department_id);
        $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id, $from_set_id);
        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterialBySetid($raw_material_id, $department_id, $from_set_id);
        if(!is_null($rawMaterialOut)){
            $rmbArr = [
                'count' => $rmdCountOut,
            ];
//            $this->rawMaterialBalanceModel->update($raw_material_id, $department_id, $rmbArr);
            $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $department_id, $from_set_id, $rmbArr);
        } else {
            $rmBalanceArr = [
                "raw_material_id"   => $raw_material_id,
                "product_id"        => $request->product_id,
                "count"             => $rmdCountOut,
                "price"             => $request->price,
                "department_id"     => $department_id,
                "set_id"            => $from_set_id,
            ];

            $this->rawMaterialBalanceModel->add($rmBalanceArr);
        }

        //Step 3: Raw Material Balance table dagi kirim qilingan rawMaterialni malum bulimda update qilamiz
        $this->rawMaterialBalanceModel->deleteBySetid($raw_material_id, $to_department_id, $set_id);
//        $rmdCountIn     = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $to_department_id);
//        if($rmdCountIn > 0){
//            $rmbArr = ['count' => $rmdCountIn];
//            $this->rawMaterialBalanceModel->update($raw_material_id, $to_department_id, $rmbArr);
//        } else {
//            $this->rawMaterialBalanceModel->delete($raw_material_id, $to_department_id);
//        }

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, 2, $department_id);
    }
}
