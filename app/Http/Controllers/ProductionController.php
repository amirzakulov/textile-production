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
use phpDocumentor\Reflection\Types\Collection;

class ProductionController extends Controller
{
    private $inoutSetsModel;
    private $rawMaterialsModel;
    private $rawMaterialDetailsModel;
    private $rawMaterialBalanceModel;
    private $departmentsModel;
    private $productsModel;

    public function __construct(){
        $this->inoutSetsModel           = new InOutSetsModel();
        $this->rawMaterialsModel        = new RawMaterialsModel();
        $this->rawMaterialDetailsModel  = new RawMaterialDetailsModel();
        $this->rawMaterialBalanceModel  = new RawMaterialBalanceModel();
        $this->departmentsModel         = new DepartmentsModel();
        $this->productsModel            = new ProductsModel();
    }

    /** ****************************** START ACTIONS ********************************** **/

    public function addProduct(Request $request){
        $this->validate($request, [
            'name'          => 'required',
            'measurement'   => 'required',
            'fashion_id'    => 'required',
            'department_id' => 'required',
        ]);

        $arr = [
            'name'          => $request->name,
            'code'          => $request->code,
            'color'         => null,
            'country'       => null,
            'measurement'   => $request->measurement,
            'picture'       => '',
            'description'   => null,
            'min_count'     => null,
            'fashion_id'    => $request->fashion_id,
            'department_id' => $request->department_id,
            'isRawMaterial' => 0,
            'category_id'   => null,
        ];

        return $this->productsModel->addProduct($arr);
    }

    public function editProduct(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required',
            'code'          => 'required',
            'measurement'   => 'required',
            'fashion_id'    => 'required',
        ]);

        $arr = [
            'name'          => $request->name,
            'code'          => $request->code,
            'measurement'   => $request->measurement,
            'fashion_id'    => $request->fashion_id,
        ];

        return $this->productsModel->update($request->id, $arr);
    }

    public function deleteProduct(Request $request) {
        $this->validate($request, [
            'id' => "required"
        ]);

        return $this->productsModel->delete($request->id);

    }

    public function addFinishProduct(Request $request)
    {
        $finishProduct = $request->finishProduct;
        $rawMaterials  = $request->rawMaterials;
        $department_id = $request->department_id;

        $created_date = dateToDateTime($request->created_date);
        $finishProductArr = [
            "product_id"    => $finishProduct["product_id"],
            "count"         => $finishProduct["count"],
        ];

        $finishProductRawMaterial  = $this->createFinishProduct($department_id, $finishProduct, $finishProductArr, $rawMaterials, $created_date);

        $finishProductData = [
            "finish_raw_material_id"=> $finishProductRawMaterial->id,
            "finish_product_id"     => $finishProduct["product_id"],
        ];
        $inoutSet = [
            "from_department_id" => $department_id,
            "to_department_id"   => $department_id,
        ];
        $set = $this->addFinishProductRawMaterials($finishProductData, $inoutSet, $rawMaterials, $department_id, $created_date);

        return $this->inoutSetsModel->getRawMaterialSets($department_id, 0);

    }

    private function createFinishProduct($department_id, $finishProduct, $finishProductArr, $rawMaterials, $created_date)
    {
        //step 1 : create partiya
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');

        $inoutSetArr = [
            'name'              => $inoutSetName,
            'user_id'           => 1,
            'inout'             => 1,
            'from_department_id'=> $department_id,
            'to_department_id'  => $department_id,
            'department_id'     => $department_id,
            'isRawMaterial'     => 0,
            'created_date'      => $created_date,
        ];

        $set = $this->inoutSetsModel->add($inoutSetArr);

        //step 2 : create Raw Material
        $total = 0;
        foreach ($rawMaterials as $key => $rawMaterial) {
//            $raw_material_data = $this->rawMaterialsModel->getRawMaterial($rawMaterial['raw_material_id']);
            $raw_material_data = $this->rawMaterialBalanceModel->getRawMaterial($rawMaterial["raw_material_balance_id"]);
            $product_cost = $rawMaterial["count"] * $raw_material_data->price;
            $total       += $product_cost;
        }

        $price = $total / $finishProduct["count"];

        $rmArr = [
            "product_id"    => $finishProductArr["product_id"],
            "price"         => $price,
            "currency_id"   => 2,
            "price_value"   => $price,
            "currency_rate" => 1,
            "set_id"        => $set->id,
            "set_name"      => $inoutSetName,
            "department_id" => $department_id,
        ];

        $rm = $this->rawMaterialsModel->add($rmArr);

        //step 3 : create Raw Material Details
        $rmdArr = [
            "raw_material_id"   => $rm->id,
            "product_id"        => $finishProductArr["product_id"],
            "set_id"            => $set->id,
            "from_set_id"       => null,
            "count"             => $finishProductArr["count"],
            "inout"             => 1,
            "user_id"           => 1,
            "from_department_id"=> $department_id,
            "to_department_id"  => $department_id,
            "department_id"     => $department_id,
            'created_date'      => $created_date,
        ];

        $rmd = $this->rawMaterialDetailsModel->add($rmdArr);

        //step 4 : create Raw Material Balance
        $rmBalanceArr = [
            "raw_material_id"   => $rm->id,
            "product_id"        => $finishProductArr["product_id"],
            "count"             => $finishProductArr["count"],
            "price"             => $price,
            "department_id"     => $department_id,
            "set_id"            => $set->id,
        ];

        $this->rawMaterialBalanceModel->add($rmBalanceArr);

        return $rm;

    }

    private function addFinishProductRawMaterials($finishProductData, $inoutSet, $rawMaterials, $department_id, $created_date)
    {
        //step 1 : Partiya yaratamiz
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');
        $inoutSetArr  = [
            'name'              => $inoutSetName,
            'user_id'           => 1,
            'inout'             => 2,
            'from_department_id'=> $inoutSet["from_department_id"],
            'to_department_id'  => $inoutSet["to_department_id"],
            'department_id'     => $department_id,
            'isRawMaterial'     => 1,
            'created_date'      => $created_date,
        ];

        $set = $this->inoutSetsModel->add($inoutSetArr);

        //step 2 : create Raw Material Details
        foreach ($rawMaterials as $product) {
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
                "user_id"           => 1,
                "from_department_id"=> $inoutSet["from_department_id"],
                "to_department_id"  => $inoutSet["to_department_id"],
                "finish_raw_material_id"=> $finishProductData["finish_raw_material_id"],
                "finish_product_id"     => $finishProductData["finish_product_id"],
                'created_date'      => $created_date,
            ];

            $this->rawMaterialDetailsModel->add($rmdArr);

            //step 3 : create Raw Material Balance
            //chiqim qilamiz
            $count = $rawMaterial->count - $product["count"];
            if($count > 0){
                $rmBalanceArr = ["count" => $count];
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $rmBalanceArr);
                $this->rawMaterialBalanceModel->update($product["raw_material_balance_id"], $rmBalanceArr);
            } else {
//                $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id);
                $this->rawMaterialBalanceModel->delete($product["raw_material_balance_id"]);
            }

        }

        return $set;
    }

    //Tayyor maxsulotlarning chiqimi
    public function addOutgoingProducts(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $products       = $request->products;
        $department_id  = $request->department_id;
        $created_date = dateToDateTime($request->created_date);

        //step 1 : Partiya yaratamiz
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');
        $inoutSetArr = [
            'name'              => $inoutSetName,
            'user_id'           => 1,
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
//            $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"], $department_id, );
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
                "user_id"           => 1,
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
                "user_id"           => 1,
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
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $rmBalanceArr);
                $this->rawMaterialBalanceModel->update($product["raw_material_balance_id"], $rmBalanceArr);
            } else {
//                $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id);
                $this->rawMaterialBalanceModel->delete($product["raw_material_balance_id"]);
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

        return $this->inoutSetsModel->getRawMaterialSets($department_id, 0);
    }

    public function addRawMaterialOutgoingProduct(Request $request) {
        $inoutSet                = $request->inoutSet;
        $product                 = $request->rawMaterial;
        $department_id           = $request->department_id;
        $finished_product        = $request->finished_product;
        $finish_raw_material_id  = $finished_product["finish_raw_material_id"];
        $finish_product_id       = $finished_product["finish_product_id"];
        $set_id                  = $inoutSet["set_id"];

        $set            = $this->inoutSetsModel->getSet($set_id);
        $created_date   = $set->created_date;

        $finishProduct = $this->rawMaterialDetailsModel->getFinishedProductInitialData($department_id, $finish_raw_material_id);
        if($this->rawMaterialDetailsModel->isFinishProductUsed($finishProduct->raw_material_id, $department_id, $finishProduct->set_id)) return response()->json(false);

        //step 1 : create Raw Material Details
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
            "user_id"           => 1,
            "from_department_id"=> $inoutSet["from_department_id"],
            "to_department_id"  => $inoutSet["to_department_id"],
            "finish_raw_material_id" => $finish_raw_material_id,
            "finish_product_id" => $finish_product_id,
            "created_date"      => $created_date,
        ];

        $this->rawMaterialDetailsModel->add($rmdArr);

        //step 2 : create Raw Material Balance
        //chiqim qilamiz
        $count = $rawMaterial->count - $product["count"];
        if($count > 0){
            $rmBalanceArr   = ["count" => $count];
            $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->raw_material_id, $department_id, $rawMaterial->set_id, $rmBalanceArr);
//            $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $rmBalanceArr);
        } else {
            $this->rawMaterialBalanceModel->deleteBySetid($rawMaterial->raw_material_id, $department_id, $rawMaterial->set_id);
//            $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id);
        }

        //Step 3: Tayyor maxsulot narxini update qilamiz
        $inoutSetRawMaterials = $this->updateFinishedProduct($department_id, $finish_raw_material_id, $set_id, 2);

        return $inoutSetRawMaterials;
    }

    public function editRawMaterialOutgoingProduct(Request $request)
    {
        $this->validate($request, [
            'count'                  => "required",
            'set_id'                 => "required",
            'department_id'          => "required",
            'from_department_id'     => "required",
            'finish_raw_material_id' => "required",
        ]);

        $count                  = $request->count;
        $stockCount             = $request->stockCount + $request->outCount;
        $department_id          = $request->department_id;
        $from_department_id     = $request->from_department_id;
        $finish_raw_material_id = $request->finish_raw_material_id;
        $set_id                 = $request->set_id;
        $from_set_id            = $request->from_set_id;

        $finishProduct = $this->rawMaterialDetailsModel->getFinishedProductInitialData($department_id, $finish_raw_material_id);

        if($this->rawMaterialDetailsModel->isFinishProductUsed($finishProduct->raw_material_id, $department_id, $finishProduct->set_id)) return response()->json(false);

        //Step 1: Raw Material Details ni update qilamiz
        $rmdOutArr = ["count" => (-1) * $count];
//        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($request->raw_material_id, $from_department_id, 2, $rmdOutArr);
        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($request->raw_material_id, $from_department_id, 2, $set_id, $rmdOutArr);

        //Step 4: Raw Material Balance ni update qilamiz
        $rmbCount = $stockCount - $count;
        $rmbOutArr = ['count' => $rmbCount];
        if($rmbCount === 0) {
//            $this->rawMaterialBalanceModel->delete($request->raw_material_id, $from_department_id);
            $this->rawMaterialBalanceModel->deleteBySetid($request->raw_material_id, $from_department_id, $from_set_id);
        } elseif ($request->stockCount === 0) {
            $rmBalanceArr = [
                "raw_material_id"   => $request->raw_material_id,
                "product_id"        => $request->product_id,
                "count"             => $rmbCount,
                "price"             => $request->price,
                "department_id"     => $department_id,
                "set_id"            => $from_set_id,
            ];
            $this->rawMaterialBalanceModel->add($rmBalanceArr);
        } else {
//            $this->rawMaterialBalanceModel->update($request->raw_material_id, $from_department_id, $rmbOutArr);
            $this->rawMaterialBalanceModel->updateBySetid($request->raw_material_id, $from_department_id, $from_set_id, $rmbOutArr);
        }

        //Step 3: Tayyor maxsulot narxini update qilamiz
        $inoutSetRawMaterials = $this->updateFinishedProduct($department_id, $finish_raw_material_id, $set_id, $request->inout);

        return $inoutSetRawMaterials;

    }

    public function deleteRawMaterialOutgoingProduct(Request $request) {
        $this->validate($request, [
            'id'            => "required",
            'set_id'        => "required",
            'department_id' => "required",
        ]);

        $raw_material_id        = $request->id;
        $department_id          = $request->department_id;
        $finish_raw_material_id = $request->finish_raw_material_id;
        $set_id                 = $request->set_id;
        $from_set_id            = $request->from_set_id;

        $finishProduct = $this->rawMaterialDetailsModel->getFinishedProductInitialData($department_id, $finish_raw_material_id);

        if($this->rawMaterialDetailsModel->isFinishProductUsed($finishProduct->raw_material_id, $department_id, $finishProduct->set_id)) return response()->json(false);

        //Step 1: Raw Material Details table dan rawMaterialni malum partiyadan chiqim va kirimini delete qilamiz
        $this->rawMaterialDetailsModel->deleteInOut($raw_material_id, $set_id);

        //Step 2: Raw Material Balance table dagi kirim qilingan rawMaterialni malum bulimda update qilamiz
//        $rmdCount = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id);
//        $rawMaterialIn = $this->rawMaterialBalanceModel->getRawMaterial($raw_material_id, $department_id);
//        if(!is_null($rawMaterialIn)){
//            $rmbArr = [
//                'count' => $rmdCount,
//            ];
//            $this->rawMaterialBalanceModel->update($raw_material_id, $department_id, $rmbArr);
//        } else {
//            $rmBalanceArr = [
//                "raw_material_id"   => $raw_material_id,
//                "product_id"        => $request->product_id,
//                "count"             => $rmdCount,
//                "price"             => $request->price,
//                "department_id"     => $department_id,
//            ];
//
//            $this->rawMaterialBalanceModel->add($rmBalanceArr);
//        }

        //Chiqim qilingan partiyaga qaytarib qushib quyamiz
        $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id, $from_set_id);
        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterialBySetid($raw_material_id, $department_id, $from_set_id);
        if(!is_null($rawMaterialOut)){
            $rmbArr = [
                'count' => $rmdCountOut,
            ];
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

        //Step 3: Tayyor maxsulot narxini update qilamiz
        $inoutSetRawMaterials = $this->updateFinishedProduct($department_id, $finish_raw_material_id, $request->set_id, $request->inout);

        return $inoutSetRawMaterials;
    }

    //Tayyor maxsulot partiyasi
    public function editFinishedProductOutSet(Request $request)
    {
        $inoutSet           = $request->inoutSet;
        $departmentInoutSet = $request->departmentInoutSet;

        $set_id             = $departmentInoutSet["id"];
        $department_id      = $departmentInoutSet["department_id"];
        $from_department_id = $departmentInoutSet["to_department_id"];
        $to_department_id   = $inoutSet["to_department_id"];

        $rawMaterials = $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $departmentInoutSet["inout"], $department_id);

        foreach ($rawMaterials as $rawMaterial) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($rawMaterial->id, $from_department_id, $set_id)) return response()->json(false);
        }

        $this->inoutSetsModel->update($set_id, ["to_department_id" => $to_department_id]);

        foreach ($rawMaterials as $rawMaterial) {
            $this->rawMaterialDetailsModel->updateBySetId($department_id, $set_id, $rawMaterial->id, ["to_department_id" => $to_department_id]);
            $this->rawMaterialDetailsModel->updateBySetId($from_department_id, $set_id, $rawMaterial->id, ["department_id" => $to_department_id,"to_department_id" => $to_department_id,]);

            //raw material balance ni update qilamiz
            $rmbArr = ["department_id" => $to_department_id];
            $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->id, $from_department_id, $set_id, $rmbArr);
//            $this->rawMaterialBalanceModel->update($rawMaterial->id, $from_department_id, ["department_id" => $to_department_id]);
        }

        return $this->inoutSetsModel->getSet($set_id);
    }

    public function deleteFinishedProductInSet(Request $request){
        $inoutSet_id        = $request->id;
        $department_id      = $request->department_id;

        //Tayyor Maxsulot
        $finishedProducts = $this->rawMaterialDetailsModel->getSetRawMaterials($inoutSet_id, $request->inout, $department_id);
        $finishedProduct = $finishedProducts[0];
        foreach ($finishedProducts as $finishProduct) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($finishProduct->id, $department_id, $inoutSet_id)) return response()->json(false);
        }

        //Step - 1 : raw tayyor maxsulotni RawMaterials tabledan uchiramiz
        $this->rawMaterialsModel->deleteBySet($inoutSet_id);

        //Step - 2 : raw tayyor maxsulotlarni RawMaterialDetails tabledan uchiramiz
        $this->rawMaterialDetailsModel->deleteBySet($inoutSet_id);

        //Step - 3 : tayyor maxsulotlarni RawMaterialBalance tabledan uchiramiz
        foreach ($finishedProducts as $finishProduct) {
            $this->rawMaterialBalanceModel->deleteByRmID($finishProduct->id);
        }

        //Xomashyolar
        //Step - 1 : raw materiallani RawMaterialDetails tabledan uchiramiz

        $rawMaterials   = $this->rawMaterialDetailsModel->getFinishedProductRawMaterials($finishedProduct->id);
        $rm_set_id = $rawMaterials[0]->set_id;
        $rm_from_set_id = $rawMaterials[0]->from_set_id;
        $this->rawMaterialDetailsModel->deleteBySet($rm_set_id);
        $this->inoutSetsModel->delete($rm_set_id);
        foreach ($rawMaterials as $rawMaterial) {

            //Step 2: Raw Material Balance table dagi chiqim qilingan bulimning rawMaterialni malum bulimda update qilamiz
            $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $department_id, $rm_from_set_id);
//            $rawMaterialBalanceOut = $this->rawMaterialBalanceModel->getRawMaterial($rawMaterial->id, $department_id);
            $rawMaterialBalanceOut = $this->rawMaterialBalanceModel->getRawMaterialBySetid($rawMaterial->id, $department_id, $rm_from_set_id);
            if(!is_null($rawMaterialBalanceOut)){
                $rmbArr = [
                    'count' => $rmdCountOut,
                ];
//                $this->rawMaterialBalanceModel->update($rawMaterial->id, $department_id, $rmbArr);
                $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->id, $department_id, $rm_from_set_id, $rmbArr);
            } else {
                $rmBalanceArr = [
                    "raw_material_id"   => $rawMaterial->id,
                    "product_id"        => $rawMaterial->product_id,
                    "count"             => $rmdCountOut,
                    "price"             => $rawMaterial->price,
                    "department_id"     => $department_id,
                    "set_id"            => $rm_from_set_id,
                ];

                $this->rawMaterialBalanceModel->add($rmBalanceArr);
            }
        }

        //Partiyani uchiramiz inout_sets tabledan
        return $this->inoutSetsModel->delete($inoutSet_id);
    }

    public function deleteFinishedProductOutSet(Request $request)
    {
        $set_id             = $request->id;
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

    private function updateFinishedProduct($department_id, $finish_raw_material_id, $set_id, $inout)
    {
        $inoutSetFinishProduct = $this->rawMaterialDetailsModel->getFinishedProductInitialData($department_id, $finish_raw_material_id);

        $inoutSetRawMaterials = [];
        if($inout == 2) {
            $inoutSetRawMaterials = $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $inout, $department_id);
        } else {
            $inoutSetRawMaterials = $this->rawMaterialDetailsModel->getFinishedProductRawMaterials($finish_raw_material_id);
        }

        //umumiy summani olamiz
        $total = 0;
        foreach ($inoutSetRawMaterials as $inoutSetRawMaterial) {
            $total += (-1) * $inoutSetRawMaterial->count * $inoutSetRawMaterial->price;
        }

        //bitta tayyor maxsulot narxini olamiz
        $price = $total / $inoutSetFinishProduct->count;

        //tayyor maxsulotning narxi raw_material_balance tableda update qilamiz
        $fpBalanceArr = ["price" => $price];
        if($inout == 1) { $fpBalanceArr["count"] = $inoutSetFinishProduct->count; }

//        $this->rawMaterialBalanceModel->update($finish_raw_material_id, $department_id, $fpBalanceArr);
        $this->rawMaterialBalanceModel->updateBySetid($finish_raw_material_id, $department_id, $set_id, $fpBalanceArr);

        //tayyor maxsulotning narxi va sonini raw_materials tableda update qilamiz
        $fpRmArr = [
            "price"       => $price,
            "price_value" => $price,
        ];
        $this->rawMaterialsModel->update($finish_raw_material_id, $fpRmArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $inout, $department_id);
    }

    public function addFinishedOutgoingProduct(Request $request)
    {
        $inoutSet         = $request->inoutSet;
        $product          = $request->rawMaterial;
        $department_id    = $request->department_id;
        $set_id           = $inoutSet["set_id"];

        $set            = $this->inoutSetsModel->getSet($set_id);
        $created_date   = $set->created_date;

        //step 1 : create Raw Material Details
//        $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $department_id, $set_id);
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
            "user_id"           => 1,
            "from_department_id"=> $inoutSet["from_department_id"],
            "to_department_id"  => $inoutSet["to_department_id"],
            "created_date"      => $created_date,
        ];

        $this->rawMaterialDetailsModel->add($rmdArr);

        //kirim qilamiz
        $rmdArr = [
            "raw_material_id"   => $rawMaterial->raw_material_id,
            "inout"             => 1,
            "department_id"     => $inoutSet["to_department_id"],
            "product_id"        => $rawMaterial->product_id,
            "set_id"            => $set_id,
            "from_set_id"       => $rawMaterial->set_id,
            "count"             => $product["count"],
            "user_id"           => 1,
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
            "set_id"            => $set_id,
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

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, 2, $department_id);
    }

    public function editFinishedIncomeProduct(Request $request)
    {
        $this->validate($request, [
            'count' => "required",
        ]);

        $department_id          = $request->department_id;
        $raw_material_id        = $request->raw_material_id;
        $raw_material_detail_id = $request->raw_material_detail_id;
        $set_id                 = $request->set_id;
        $inout                  = 1;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($raw_material_id, $department_id, $set_id)) return response()->json(false);

        //Step 1: Raw Materialning countini update qilib olamiz
        $fpRmArr = [
            "count"       => $request->count,
        ];
        $this->rawMaterialDetailsModel->update($raw_material_detail_id, $fpRmArr);

        //Step 2: Tayyor Maxsulotni update qilamiz
        return $this->updateFinishedProduct($department_id, $raw_material_id, $set_id, $inout);
    }

    public function editFinishedOutgoingProduct(Request $request)
    {
        $this->validate($request, [
            'count'           => "required",
        ]);

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
//            $this->rawMaterialBalanceModel->delete($request->raw_material_id, $from_department_id);
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
//            $this->rawMaterialBalanceModel->update($request->raw_material_id, $from_department_id, $rmbOutArr);
            $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $from_department_id, $from_set_id, $rmbOutArr);
        }

        $rmbInArr = ['count' => $count];
//        $this->rawMaterialBalanceModel->update($request->raw_material_id, $to_department_id, $rmbInArr);
        $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $to_department_id, $set_id, $rmbInArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $request->inout, $department_id);
    }

    public function deleteFinishedOutgoingProduct(Request $request) {
        $this->validate($request, [
            'id'            => "required",
            'set_id'        => "required",
            'department_id' => "required",
        ]);

        $raw_material_id  = $request->id;
        $department_id    = $request->department_id;
//        $inout_set_id     = $request->set_id;
        $to_department_id = $request->to_department_id;
        $set_id           = $request->set_id;
        $from_set_id      = $request->from_set_id;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($raw_material_id, $to_department_id, $set_id)) return response()->json(false);

        //Step 1: Raw Material Details table dan rawMaterialni malum partiyadan chiqim va kirimini delete qilamiz
        $this->rawMaterialDetailsModel->deleteInOut($raw_material_id, $set_id);

        //Step 2: Raw Material Balance table dagi chiqim qilingan rawMaterialni malum bulimda update qilamiz
//        $rmdCountOut    = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id);
        $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id, $from_set_id);
//        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterial($raw_material_id, $department_id);
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
    /** ****************************** END ACTIONS ********************************** **/

    //Bulimning xomashyo maxsulotlari jami soni bilan
    public function getDepartmentRawMaterialsStock($department_id)
    {
        return $this->rawMaterialBalanceModel->getDepartmentStockProducts($department_id, 1);
    }

    //Bulimning Tayyor maxsulotlari jami soni bilan
    public function getDepartmentFinishProductsStock($department_id) {
        $isRawMaterial = 0;             //yarim tayyor maxsulot
        $department_products = true;    //yarim tayyor maxsulotlar omborida faqat bulimning uzining yarim tayyor maxsulotlarni kursatsin

        return $this->rawMaterialBalanceModel->getDepartmentStockProducts($department_id, $isRawMaterial, $department_products);
    }

    //Bulimning Yarim Tayyor maxsulotlari jami soni bilan
    public function getDepartmentSemiFinishProductsStock($department_id) {
        $isRawMaterial = 0;             //yarim tayyor maxsulot
        $department_products = false;    //yarim tayyor maxsulotlar omborida faqat bulimning uzining yarim tayyor maxsulotlarni kursatsin

        return $this->rawMaterialBalanceModel->getDepartmentStockProducts($department_id, $isRawMaterial, $department_products);
    }

    public function getDepartmentSemiFinishProducts($department_id)
    {
        $isRawMaterial = 0;             //yarim tayyor maxsulot
        $department_products = false;    //yarim tayyor maxsulotlar omborida faqat bulimning uzining yarim tayyor maxsulotlarni kursatsin

        return $this->rawMaterialBalanceModel->getDepartmentRawMaterials($department_id, $isRawMaterial, $department_products);
    }

    //Bulimning xomashyolari
    public function getDepartmentRawMaterials($department_id) {
        $isRawMaterial = null;             //xomashyo va yarim tayyor maxsulotlar

        return $this->rawMaterialBalanceModel->getDepartmentRawMaterials($department_id, $isRawMaterial);
    }

    //Bulimning tayyor maxsulotlari
    public function getDepartmentFinishProducts($department_id) {
        $isRawMaterial = 0;             //tayyor maxsulotlar
        $department_products = true;    //faqat bulimning uzining tayyor maxsulotlari

        return $this->rawMaterialBalanceModel->getDepartmentRawMaterials($department_id, $isRawMaterial, $department_products);
    }

    public function getDepartmentProducts($department_id) {
        return $this->productsModel->getDepartmentProducts($department_id);
    }

    public function getDepartmentRawMaterialSets($department_id)
    {
        $inout = null;
        $isRawMaterial = 1;
        return $this->rawMaterialDetailsModel->getDepartmentSets($department_id, $isRawMaterial, $inout);
    }

    public function getDepartmentFinishProductsSets($department_id)
    {
        $isRawMaterial = 0;
        return $this->inoutSetsModel->getRawMaterialSets($department_id, $isRawMaterial);
    }


    public function getDepartmentSemiFinishProductsSets($department_id)
    {
        $isRawMaterial = 0;
        return $this->rawMaterialDetailsModel->getDepartmentSets($department_id, $isRawMaterial);
    }

    public function getIncomeProducts(Request $request)
    {
        $year           = strval($request->year);
        $month          = strval($request->month);
        $department_id  = $request->department_id;

        $inout = 1;
        $dateString = $year.'-'.$month.'-01';

        $date = new DateTime($dateString);
        $date->modify('first day of this month');
        $first_day_this_month = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $last_day_this_month = $date->format('Y-m-d');

        return $this->rawMaterialDetailsModel->getInOutProductsByDate($inout, $first_day_this_month, $last_day_this_month, $department_id, 0);
    }

//    public function getDepartmentRawMaterial($raw_material_id, $department_id)
//    {
//        return $this->rawMaterialBalanceModel->getRawMaterial($raw_material_id, $department_id);
//    }

    public function getRawMaterialDetails($raw_material_details_id)
    {
        return $this->rawMaterialDetailsModel->getRawMaterial($raw_material_details_id);
    }

    //Partiya maxsulotlari
    public function getSetProducts($set_id, $inout, $department_id)
    {
        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $inout, $department_id);
    }

    public function getSet($department_id, $set_id)
    {
        return $this->rawMaterialDetailsModel->getDepartmentSet($department_id, $set_id);
    }

    public function getFinishedProductRawMaterials($raw_material_id)
    {
        return $this->rawMaterialDetailsModel->getFinishedProductRawMaterials($raw_material_id);
    }

}
