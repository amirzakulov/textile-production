<?php

namespace App\Http\Controllers;
date_default_timezone_set("Asia/Tashkent");
use App\Http\Controllers\dbQueries\CurrencyRateModel;
use App\Http\Controllers\dbQueries\InOutSetsModel;
use App\Http\Controllers\dbQueries\ProductsModel;
use App\Http\Controllers\dbQueries\RawMaterialDetailsModel;
use App\Http\Controllers\dbQueries\RawMaterialBalanceModel;
use App\Http\Controllers\dbQueries\RawMaterialsModel;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RawMaterialsController extends Controller
{
    private $inoutSetsModel;
    private $rawMaterialsModel;
    private $rawMaterialDetailsModel;
    private $rawMaterialBalanceModel;
    private $productsModel;
    private $currencyRateModel;
    private $department_id = 2;

    public function __construct(){
        $this->inoutSetsModel           = new InOutSetsModel();
        $this->rawMaterialsModel        = new RawMaterialsModel();
        $this->rawMaterialDetailsModel  = new RawMaterialDetailsModel();
        $this->rawMaterialBalanceModel  = new RawMaterialBalanceModel();
        $this->productsModel            = new ProductsModel();
        $this->currencyRateModel        = new CurrencyRateModel();
    }

    public function getRawMaterial($raw_material_id)
    {
        return $this->rawMaterialsModel->getRawMaterial($raw_material_id);
    }
    /**
     * Products
     **/
    public function getProducts($department_id) {
        return $this->productsModel->getDepartmentProducts($department_id);
    }

    //Bulim maxsulotini qushish
    public function addProduct(Request $request){
        $this->validate($request, [
            'name'        => 'required',
            'measurement' => 'required',
            'category_id' => 'required',
        ]);

        $arr = [
            'name'          => $request->name,
            'code'          => $request->code,
            'color'         => $request->color,
            'country'       => $request->country,
            'measurement'   => $request->measurement,
            'picture'       => '',
            'description'   => $request->description,
            'min_count'     => $request->min_count,
            'fashion_id'    => $request->fashion_id,
            'department_id' => $this->department_id,
            'isRawMaterial' => $request->isRawMaterial,
            'category_id'   => $request->category_id,
        ];

        return $this->productsModel->addProduct($arr);
    }

    //Bulim maxsulotini taxrirlash
    public function editProduct(Request $request){
        $this->validate($request, [
            'name'        => 'required',
            'measurement' => 'required',
            'category_id' => 'required',
        ]);

        $arr = [
            'name'          => $request->name,
            'code'          => $request->code,
            'color'         => $request->color,
            'country'       => $request->country,
            'measurement'   => $request->measurement,
            'description'   => $request->description,
            'min_count'     => $request->min_count,
            'category_id'   => $request->category_id,
        ];

        return $this->productsModel->update($request->id, $arr);
    }

    //Bulim maxsulotini uchirish
    public function deleteProduct(Request $request)
    {
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->productsModel->delete($request->id);

    }

    /** ****************************** Raw Materials ******************************* **/

    //X.A Omboridagi maxsulotlar
    public function getStockProducts($category_id)
    {
        return $this->productsModel->getStockProducts($this->department_id, $category_id);
    }

    //Xom ashyo maxsulotlarining partiyalarni olamiz
    public function getSets()
    {
        $isRawMaterial = 1;
        return $this->inoutSetsModel->getRawMaterialSets($this->department_id, $isRawMaterial);
    }

    public function getSet($id)
    {
        return $this->inoutSetsModel->getSet($id);
    }

    //Partiya maxsulotlari
    public function getSetProducts($set_id, $inout)
    {
        $department_id    = 2;
        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $inout, $department_id);
    }

    public function getDepartmentRawMaterials($department_id)
    {
        return $this->rawMaterialBalanceModel->getDepartmentRawMaterials($department_id, 1, true);
    }

    public function getRawMaterialByBalanceId($raw_material_balance_id)
    {
        return $this->rawMaterialBalanceModel->getRawMaterial($raw_material_balance_id);
    }

    public function getDepartmentRawMaterial($raw_material_id, $department_id, $set_id)
    {
        return $this->rawMaterialBalanceModel->getRawMaterialBySetid($raw_material_id, $department_id, $set_id);
    }

    public function getRawMaterialDetails($raw_material_details_id)
    {
        return $this->rawMaterialDetailsModel->getRawMaterial($raw_material_details_id);
    }

    /** ************************** Income products *********************** **/
    /**
     * @throws \Exception
     */
    public function addIncomeProducts(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $products       = $request->products;
        $department_id  = $request->department_id;

        $created_date = dateToDateTime($request->created_date);
        $user         = Auth::user();

        //step 1 : create partiya
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');
        $inoutSetArr = [
            'name'              => $inoutSetName,
            'user_id'           => $user->id,
            'inout'             => 1,
            'from_department_id'=> $inoutSet["from_department_id"],
            'to_department_id'  => $inoutSet["to_department_id"],
            'department_id'     => $department_id,
            'isRawMaterial'     => 1,
            'created_date'      => $created_date,
        ];

        $set = $this->inoutSetsModel->add($inoutSetArr);

        $dollar = $this->currencyRateModel->getLastCurrencyRate(1);
        foreach ($products as $product) {
            $currency_rate  = 1;
            $price          = $product["price_value"];
            if($product["currency_id"] == 1) {
                $currency_rate  = $dollar->rate;
                $price          = $currency_rate * $product["price_value"];
            }

            //step 2 : create Raw Materials
            $rmArr = [
                "product_id"    => $product["product_id"],
                "price"         => $price,
                "currency_id"   => $product["currency_id"],
                "price_value"   => $product["price_value"],
                "currency_rate" => $currency_rate,
                "set_id"        => $set->id,
                "set_name"      => $inoutSetName,
                "department_id" => $department_id,
            ];

            $rm = $this->rawMaterialsModel->add($rmArr);

            //step 3 : create Raw Material Details
            $rmdArr = [
                "raw_material_id"   => $rm->id,
                "product_id"        => $product["product_id"],
                "set_id"            => $set->id,
                "from_set_id"       => null,
                "count"             => $product["count"],
                "inout"             => 1,
                'user_id'           => $user->id,
                "from_department_id"=> $inoutSet["from_department_id"],
                "to_department_id"  => $inoutSet["to_department_id"],
                "department_id"     => $department_id,
                'created_date'      => $created_date,
            ];

            $rmd = $this->rawMaterialDetailsModel->add($rmdArr);

            //step 4 : create Raw Material Balance
            $rmBalanceArr = [
                "raw_material_id"   => $rm->id,
                "product_id"        => $product["product_id"],
                "count"             => $product["count"],
                "price"             => $price,
                "department_id"     => $department_id,
                "set_id"            => $set->id,
            ];

            $this->rawMaterialBalanceModel->add($rmBalanceArr);
        }

        return $set;
    }

    public function deleteInSet(Request $request)
    {
        $inoutSet_id    = $request->id;
        $department_id  = $request->department_id;

        $rawMaterials = $this->rawMaterialDetailsModel->getSetRawMaterials($inoutSet_id, $request->inout, $department_id);

        foreach ($rawMaterials as $rawMaterial) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($rawMaterial->id, $department_id, $inoutSet_id)) return response()->json(false);
        }

        //Step - 1 : raw materiallani RawMaterials tabledan uchiramiz
        $this->rawMaterialsModel->deleteBySet($inoutSet_id);

        //Step - 2 : raw materiallani RawMaterialDetails tabledan uchiramiz
        $this->rawMaterialDetailsModel->deleteBySet($inoutSet_id);

        //Step - 3 : raw materiallani RawMaterialBalance tabledan uchiramiz
        foreach ($rawMaterials as $rawMaterial) {
            $this->rawMaterialBalanceModel->deleteByRmID($rawMaterial->id);
        }

        //Partiyani uchiramiz inout_sets tabledan
        return $this->inoutSetsModel->delete($inoutSet_id);
    }

    public function addIncomeProduct(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $product        = $request->rawMaterial;
        $department_id  = $request->department_id;

        $set            = $this->inoutSetsModel->getSet($inoutSet["id"]);
        $created_date   = $set->created_date;
        $user           = Auth::user();

        $dollar         = $this->currencyRateModel->getLastCurrencyRate(1);
        $currency_rate  = 1;
        $price          = $product["price_value"];
        if($product["currency_id"] == 1) {
            $currency_rate  = $dollar->rate;
            $price          = $currency_rate * $product["price_value"];
        }

        //step 2 : create Raw Materials
        $rmArr = [
            "product_id"    => $product["product_id"],
            "price"         => $price,
            "currency_id"   => $product["currency_id"],
            "price_value"   => $product["price_value"],
            "currency_rate" => $currency_rate,
            "set_id"        => $inoutSet["id"],
            "set_name"      => $inoutSet["name"],
            "department_id" => $department_id,
        ];

        $rm = $this->rawMaterialsModel->add($rmArr);

        //step 3 : create Raw Material Details
        $rmdArr = [
            "raw_material_id"   => $rm->id,
            "product_id"        => $product["product_id"],
            "set_id"            => $inoutSet["id"],
            "from_set_id"       => null,
            "count"             => $product["count"],
            "inout"             => 1,
            "user_id"           => $user->id,
            "from_department_id"=> $inoutSet["from_department_id"],
            "to_department_id"  => $inoutSet["to_department_id"],
            "department_id"     => $department_id,
            "created_date"     => $created_date,
        ];

        $rmd = $this->rawMaterialDetailsModel->add($rmdArr);

        //step 4 : create Raw Material Balance
        $rmBalanceArr = [
            "raw_material_id"   => $rm->id,
            "product_id"        => $product["product_id"],
            "count"             => $product["count"],
            "price"             => $price,
            "department_id"     => $department_id,
            "set_id"            => $inoutSet["id"],
        ];

        $this->rawMaterialBalanceModel->add($rmBalanceArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($inoutSet["id"], 1, $department_id);
    }

    public function editIncomeProduct(Request $request)
    {
        if($this->rawMaterialDetailsModel->isRawMaterialUsed($request->raw_material_id, $request->department_id, $request->set_id)) return response()->json(false);

        //Step 1: valyuta turiga qarab valyuta qiymatini va price ni olvolamiz
        $currency_rate  = 1;
        $price          = $request->price_value;
        if($request->currency_id == 1) {
            $dollar         = $this->currencyRateModel->getLastCurrencyRate(1);
            $currency_rate  = $dollar->rate;
            $price          = $currency_rate * $request->price_value;
        }

        //Step 2: Raw Material ni update qilamiz
        $rmArr = [
            "price"         => $price,
            "currency_id"   => $request->currency_id,
            "price_value"   => $request->price_value,
            "currency_rate" => $currency_rate,
        ];
        $this->rawMaterialsModel->update($request->raw_material_id, $rmArr);

        //Step 3: Raw Material Detail ni update qilamiz
        $rmdArr = [
            "count" => $request->count,
        ];
        $rmd = $this->rawMaterialDetailsModel->update($request->raw_material_detail_id, $rmdArr);

        //Step 4: Raw Material Balance ni update qilamiz
        $rmbCount = $this->rawMaterialDetailsModel->getRawMaterialCount($request->raw_material_id, $request->department_id, $request->set_id);
        $rmbArr = [
            'count' => $rmbCount,
            'price' => $price,
        ];
//        $this->rawMaterialBalanceModel->update($request->raw_material_id, $request->department_id, $request->set_id, $rmbArr);
        $this->rawMaterialBalanceModel->updateBySetid($request->raw_material_id, $request->department_id, $request->set_id, $rmbArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($request->set_id, $request->inout, $request->department_id);
    }

    public function deleteIncomeProduct(Request $request)
    {

        $this->validate($request, [
            'id'                     => "required",
            'raw_material_detail_id' => "required",
            'department_id'          => "required",
        ]);

        $raw_material_id        = $request->id;
        $raw_material_detail_id = $request->raw_material_detail_id;
        $department_id          = $request->department_id;
        $set_id                 = $request->set_id;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($raw_material_id, $department_id, $set_id)) return response()->json(false);

        $this->rawMaterialDetailsModel->delete($raw_material_detail_id);
        $this->rawMaterialBalanceModel->deleteByRmID($raw_material_id);
        $this->rawMaterialsModel->delete($raw_material_id);

        $setProducts = $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $request->inout, $department_id);
        if(count($setProducts) === 0) {
            $this->inoutSetsModel->delete($set_id);
        }

        return $setProducts;
    }

    /** ************************** Outgoing products *********************** **/
    public function addOutgoingProducts(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $products       = $request->products;
        $department_id  = $request->department_id;
        $created_date   = $request->created_date;

        $dateTime = dateToDateTime($created_date);
        $user         = Auth::user();

        //step 1 : Partiya yaratamiz
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');
        $inoutSetArr = [
            'name'              => $inoutSetName,
            'user_id'           => 1,
            'inout'             => 2,
            'from_department_id'=> $inoutSet["from_department_id"],
            'to_department_id'  => $inoutSet["to_department_id"],
            'department_id'     => $department_id,
            'isRawMaterial'     => 1,
            'created_date'      => $dateTime,
        ];

        $set = $this->inoutSetsModel->add($inoutSetArr);

        //step 2 : create Raw Material Details
        foreach ($products as $product) {
//            $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"], $department_id);
            $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"]);

            //chiqim qilamiz
            $rmdArr = [
//                "raw_material_id"   => $product["raw_material_id"],
                "raw_material_id"   => $rawMaterial->raw_material_id,
                "inout"             => 2,
                "department_id"     => $department_id,
                "product_id"        => $rawMaterial->product_id,
                "set_id"            => $set->id,
                "from_set_id"       => $rawMaterial->set_id,
                "count"             => (-1) * $product["count"],
                "user_id"           => $user->id,
                "from_department_id"=> $inoutSet["from_department_id"],
                "to_department_id"  => $inoutSet["to_department_id"],
                'created_date'      => $dateTime,
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
                "user_id"           => $user->id,
                "from_department_id"=> $inoutSet["from_department_id"],
                "to_department_id"  => $inoutSet["to_department_id"],
                'created_date'      => $dateTime,
            ];

            $this->rawMaterialDetailsModel->add($rmdArr);

            //step 3 : create Raw Material Balance
            //chiqim qilamiz
            $count = $rawMaterial->count - $product["count"];
            if($count > 0){
                $rmBalanceArr   = ["count" => $count];
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $request->set_id, $rmBalanceArr);
                $this->rawMaterialBalanceModel->update($product["raw_material_balance_id"], $rmBalanceArr);
            } else {
//                $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id, $request->set_id);
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
//            $rawMaterialIn = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $inoutSet["to_department_id"], $request->set_id);
//            if(!empty($rawMaterialIn)){
//                $count          = $rawMaterialIn->count + $product["count"];
//                $rmBalanceArr   = ["count" => $count];
//
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $inoutSet["to_department_id"], $request->set_id, $rmBalanceArr);
//            } else {
//                $rmBalanceArr = [
//                    "raw_material_id"   => $product["raw_material_id"],
//                    "product_id"        => $rawMaterial->product_id,
//                    "count"             => $product["count"],
//                    "price"             => $rawMaterial->price,
//                    "department_id"     => $inoutSet["to_department_id"],
//                    "set_id"            => $set->id,
//                ];
//
//                $this->rawMaterialBalanceModel->add($rmBalanceArr);
//            }
        }

        return $set;
    }

    public function editOutSet(Request $request){
        $inoutSet = $request->inoutSet;
        $rmSet    = $request->rmSet;

        $set_id             = $rmSet["id"];
        $from_department_id = $rmSet["to_department_id"];
        $to_department_id   = $inoutSet["to_department_id"];

        $rawMaterials = $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $rmSet["inout"], $rmSet["department_id"]);
        //chiqim qilingan raw material, chiqim qilingan departmentda ishlatilgan bulsa, uni uchirishga ruxsat bermaymiz
        foreach ($rawMaterials as $rawMaterial) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($rawMaterial->id, $from_department_id, $set_id)) return response()->json(false);
        }

        $this->inoutSetsModel->update($set_id, ["to_department_id" => $inoutSet["to_department_id"]]);

        foreach ($rawMaterials as $rawMaterial) {

            $this->rawMaterialDetailsModel->updateBySetId($rmSet["department_id"], $set_id, $rawMaterial->id, ["to_department_id" => $to_department_id]);
            $this->rawMaterialDetailsModel->updateBySetId($from_department_id, $set_id, $rawMaterial->id, ["department_id" => $to_department_id,"to_department_id" => $to_department_id]);

            //raw material balance ni update qilamiz
            $rmbArr = ["department_id" => $to_department_id];
            $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->id, $from_department_id, $set_id, $rmbArr);

            //Eski bulimdan
//            $fromRmdCount = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $from_department_id, $rmSet["id"]);
//            if($fromRmdCount > 0){
//                $rmbArr = [
//                    'count' => $fromRmdCount,
//                ];
//                $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->id, $from_department_id, $rmSet["id"], $rmbArr);
//            } else {
//                $this->rawMaterialBalanceModel->deleteBySetid($rawMaterial->id, $from_department_id, $rmSet["id"]);
//            }

            //Yangi bulimga
//            $toRmdCount = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $to_department_id, $rmSet["id"]);
//            $toRawMaterialBalance = $this->rawMaterialBalanceModel->getRawMaterialBySetid($rawMaterial->id, $to_department_id, $rmSet["id"]);
//            if(!is_null($toRawMaterialBalance)){
//                $rmbUpdateArr = [
//                    'count' => $toRmdCount,
//                ];
//                $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->id, $to_department_id, $rmbUpdateArr, $rmSet["id"]);
//            } else {
//                $rmbAddArr = [
//                    "raw_material_id"   => $rawMaterial->id,
//                    "product_id"        => $rawMaterial->product_id,
//                    "count"             => $toRmdCount,
//                    "price"             => $rawMaterial->price,
//                    "department_id"     => $to_department_id,
//                    "set_id"            => $rmSet["id"],
//                ];
//
//                $this->rawMaterialBalanceModel->add($rmbAddArr);
//            }
        }

        return $this->inoutSetsModel->getSet($set_id);
    }

    public function deleteOutSet(Request $request)
    {
        $inoutSet_id        = $request->id;
        $department_id      = $request->department_id;
        $to_department_id   = $request->to_department_id;

        $rawMaterials = $this->rawMaterialDetailsModel->getSetRawMaterials($inoutSet_id, $request->inout, $department_id);

        foreach ($rawMaterials as $rawMaterial) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($rawMaterial->id, $to_department_id, $inoutSet_id)) return response()->json(false);
        }

        //Step - 1 : raw materiallani RawMaterialDetails tabledan uchiramiz
        $this->rawMaterialDetailsModel->deleteBySet($inoutSet_id);

        foreach ($rawMaterials as $rawMaterial) {

            //Step 2: Raw Material Balance table dagi chiqim qilingan bulimning rawMaterialni malum bulimda update qilamiz
            $rmdCountOut           = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $department_id, $rawMaterial->from_set_id);
            $rawMaterialBalanceOut = $this->rawMaterialBalanceModel->getRawMaterialBySetid($rawMaterial->id, $department_id, $rawMaterial->from_set_id);
            if(!is_null($rawMaterialBalanceOut)){
                $rmbArr = [
                    'count' => $rmdCountOut,
                ];
//                $this->rawMaterialBalanceModel->update($rawMaterial->id, $department_id, $inoutSet_id, $rmbArr);
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
            $this->rawMaterialBalanceModel->deleteBySetid($rawMaterial->id, $to_department_id, $inoutSet_id);
//            $rmdCountIn = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $to_department_id, $inoutSet_id);
//            if($rmdCountIn > 0){
//                $rmbArr = [
//                    'count' => $rmdCountIn,
//                ];
//                $this->rawMaterialBalanceModel->update($rawMaterial->id, $to_department_id, $inoutSet_id, $rmbArr);
//            } else {
//                $this->rawMaterialBalanceModel->delete($rawMaterial->id, $to_department_id, $inoutSet_id);
//            }
        }

        //Partiyani uchiramiz inout_sets tabledan
        return $this->inoutSetsModel->delete($inoutSet_id);
    }

    public function addOutgoingProduct(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $product        = $request->rawMaterial;
        $department_id  = $request->department_id;

        $set            = $this->inoutSetsModel->getSet($inoutSet["id"]);
        $created_date   = $set->created_date;
        $user         = Auth::user();

        //step 1 : create Raw Material Details
//        $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"], $department_id, $inoutSet["id"]);
        $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_balance_id"]);
        //chiqim qilamiz
        $rmdArr = [
            "raw_material_id"   => $rawMaterial->raw_material_id,
            "inout"             => 2,
            "department_id"     => $department_id,
            "product_id"        => $rawMaterial->product_id,
            "set_id"            => $inoutSet["id"],
            "from_set_id"       => $rawMaterial->set_id,
            "count"             => (-1) * $product["count"],
            "user_id"           => $user->id,
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
            "set_id"            => $inoutSet["id"],
            "from_set_id"       => $rawMaterial->set_id,
            "count"             => $product["count"],
            "user_id"           => $user->id,
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

//            $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $inoutSet["id"], $rmBalanceArr);
            $this->rawMaterialBalanceModel->updateBySetid($rawMaterial->raw_material_id, $department_id, $rawMaterial->set_id, $rmBalanceArr);
        } else {
//            $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id, $inoutSet["id"]);
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

//        $rawMaterialIn = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $inoutSet["to_department_id"], $inoutSet["id"]);
//        if(!empty($rawMaterialIn)){
//            $count          = $rawMaterial->count + $product["count"];
//            $rmBalanceArr   = ["count" => $count];
//
//            $this->rawMaterialBalanceModel->update($product["raw_material_id"], $inoutSet["to_department_id"], $inoutSet["id"], $rmBalanceArr);
//        } else {
//            $rmBalanceArr = [
//                "raw_material_id"   => $product["raw_material_id"],
//                "product_id"        => $rawMaterial->product_id,
//                "count"             => $product["count"],
//                "price"             => $rawMaterial->price,
//                "department_id"     => $inoutSet["to_department_id"],
//                "set_id"            => $inoutSet["id"],
//            ];
//
//            $this->rawMaterialBalanceModel->add($rmBalanceArr);
//        }

        return $this->rawMaterialDetailsModel->getSetRawMaterials($inoutSet["id"], 2, $department_id);
    }

    public function editOutgoingProduct(Request $request)
    {
        $count              = $request->count;
        $stockCount         = $request->stockCount + $request->outCount;
        $department_id      = $request->department_id;
        $from_department_id = $request->from_department_id;
        $to_department_id   = $request->to_department_id;
        $set_id             = $request->set_id;
        $from_set_id        = $request->from_set_id;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($request->raw_material_id, $to_department_id, $request->set_id)) return response()->json(false);

        //Step 1: Raw Material Details ni update qilamiz
        $rmdOutArr = ["count" => (-1) * $count];
        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($request->raw_material_id, $from_department_id, 2, $set_id, $rmdOutArr);

        $rmdInArr = ["count" => $count];
        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($request->raw_material_id, $to_department_id, 1, $set_id, $rmdInArr);

        //Step 4: Raw Material Balance ni update qilamiz
        $rmbCount = $stockCount - $count;
        $rmbOutArr = ['count' => $rmbCount];
        if($rmbCount === 0) {
//            $this->rawMaterialBalanceModel->delete($request->raw_material_id, $from_department_id, $request->set_id);
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
//            $this->rawMaterialBalanceModel->update($request->raw_material_id, $from_department_id, $request->set_id, $rmbOutArr);
            $this->rawMaterialBalanceModel->updateBySetid($request->raw_material_id, $from_department_id, $from_set_id, $rmbOutArr);
        }

        $rmbInArr = ['count' => $count];
//        $this->rawMaterialBalanceModel->update($request->raw_material_id, $to_department_id, $request->set_id, $rmbInArr);
        $this->rawMaterialBalanceModel->updateBySetid($request->raw_material_id, $to_department_id, $set_id, $rmbInArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($request->set_id, $request->inout, $department_id);
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
        $to_department_id = $request->to_department_id;
        $set_id           = $request->set_id;
        $from_set_id      = $request->from_set_id;

        if($this->rawMaterialDetailsModel->isRawMaterialUsed($raw_material_id, $to_department_id, $set_id)) return response()->json(false);

        //Step 1: Raw Material Details table dan rawMaterialni malum partiyadan chiqim va kirimini delete qilamiz
        $this->rawMaterialDetailsModel->deleteInOut($raw_material_id, $set_id);

        //Step 2: Raw Material Balance table dagi chiqim qilingan rawMaterialni malum bulimda update qilamiz
        $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id, $from_set_id);
//        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterial($raw_material_id, $department_id, $set_id);
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

        //Step 3: Raw Material Balance table dagi kirim qilingan rawMaterialni malum bulimda update qilamiz
        $this->rawMaterialBalanceModel->deleteBySetid($raw_material_id, $to_department_id, $set_id);
//        $rmdCountIn     = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $to_department_id, $set_id);
//        if($rmdCountIn > 0){
//            $rmbArr = ['count' => $rmdCountIn];
//            $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $to_department_id, $set_id, $rmbArr);
//        } else {
//            $this->rawMaterialBalanceModel->deleteBySetid($raw_material_id, $to_department_id, $set_id);
//        }

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, 2, $department_id);
    }

    /** **************************** Semi Finished Products ************************* **/
    public function getSemiFinishProductsSet($department_id, $set_id)
    {
        return $this->rawMaterialDetailsModel->getDepartmentSet($department_id, $set_id);
    }

    public function getSemiFinishProductsSets($department_id)
    {
        $isRawMaterial = 0;
        return $this->rawMaterialDetailsModel->getDepartmentSets($department_id, $isRawMaterial);
    }

    public function getSemiFinishStockProducts($department_id)
    {
        $isRawMaterial = 0;
        $department_products = false;

        return $this->rawMaterialBalanceModel->getDepartmentStockProducts($department_id, $isRawMaterial, $department_products);
    }

    public function getSemiFinishProducts($department_id) {
        $isRawMaterial = 0;             //yarim tayyor maxsulot
        $department_products = false;    //yarim tayyor maxsulotlar omborida faqat bulimning uzining yarim tayyor maxsulotlarni kursatsin

        return $this->rawMaterialBalanceModel->getDepartmentRawMaterials($department_id, $isRawMaterial, $department_products);
    }

    public function addOutgoingSemiFinishProducts(Request $request)
    {
        $inoutSet       = $request->inoutSet;
        $products       = $request->products;
        $department_id  = $request->department_id;
        $created_date   = dateToDateTime($request->created_date);
        $user         = Auth::user();

        //step 1 : Partiya yaratamiz
        $inoutSetName = idGenerate('inout_sets', 'name', 10, 'PT-');
        $inoutSetArr = [
            'name'              => $inoutSetName,
            'user_id'           => $user->id,
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
//            $rawMaterial = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $department_id, $set->id);
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
                "user_id"           => $user->id,
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
                "user_id"           => $user->id,
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
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $department_id, $set->id, $rmBalanceArr);
                $this->rawMaterialBalanceModel->update($product["raw_material_balance_id"], $rmBalanceArr);
            } else {
//                $this->rawMaterialBalanceModel->delete($product["raw_material_id"], $department_id, $set->id);
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
//            $rawMaterialIn = $this->rawMaterialBalanceModel->getRawMaterial($product["raw_material_id"], $inoutSet["to_department_id"], $set->id);
//            if(!empty($rawMaterialIn)){
//                $count          = $rawMaterial->count + $product["count"];
//                $rmBalanceArr   = ["count" => $count];
//
//                $this->rawMaterialBalanceModel->update($product["raw_material_id"], $inoutSet["to_department_id"], $set->id, $rmBalanceArr);
//            } else {
//                $rmBalanceArr = [
//                    "raw_material_id"   => $product["raw_material_id"],
//                    "product_id"        => $rawMaterial->product_id,
//                    "count"             => $product["count"],
//                    "price"             => $rawMaterial->price,
//                    "department_id"     => $inoutSet["to_department_id"],
//                    "set_id"            => $set->id,
//                ];
//
//                $this->rawMaterialBalanceModel->add($rmBalanceArr);
//            }
        }

        $isRawMaterial = 0;
        return $this->rawMaterialDetailsModel->getDepartmentSets($department_id, $isRawMaterial);
//        return $this->inoutSetsModel->getRawMaterialSets($department_id, 0);
    }

    public function deleteSemiFinishProductsOutSet(Request $request)
    {
        $set_id        = $request->set_id;
        $department_id      = $request->department_id;
        $to_department_id   = $request->to_department_id;

        $rawMaterials = $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, $request->inout, $department_id);
        foreach ($rawMaterials as $rawMaterial) {
            if($this->rawMaterialDetailsModel->isRawMaterialUsed($rawMaterial->id, $to_department_id, $set_id)) return response()->json(false);
        }

        //Step - 1 : raw materiallani RawMaterialDetails tabledan uchiramiz
        $this->rawMaterialDetailsModel->deleteBySet($set_id);
        foreach ($rawMaterials as $rawMaterial) {
            //Step 2: Raw Material Balance table dagi chiqim qilingan bulimning rawMaterialni malum bulimda update qilamiz
//            $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $department_id, $set_id);
//            $rawMaterialBalanceOut = $this->rawMaterialBalanceModel->getRawMaterial($rawMaterial->id, $department_id, $set_id);
            $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $department_id, $rawMaterial->from_set_id);
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
//            $rmdCountIn = $this->rawMaterialDetailsModel->getRawMaterialCount($rawMaterial->id, $to_department_id, $set_id);
//            if($rmdCountIn > 0){
//                $rmbArr = [
//                    'count' => $rmdCountIn,
//                ];
//                $this->rawMaterialBalanceModel->update($rawMaterial->id, $to_department_id, $set_id, $rmbArr);
//            } else {
//
//                $this->rawMaterialBalanceModel->delete($rawMaterial->id, $to_department_id, $set_id);
//            }
        }

        //Partiyani uchiramiz inout_sets tabledan
        return $this->inoutSetsModel->delete($set_id);
    }

    /** **************************** Additional functions ************************* **/
    public function getMeasurements()
    {
        return getMeasurements();
    }

    public function editSemiFinishProductOut(Request $request)
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
        $rmd = $this->rawMaterialDetailsModel->updateByDepartmentId($request->raw_material_id, $to_department_id, 1, $set_id, $rmdInArr);

        //Step 4: Raw Material Balance ni update qilamiz
        $rmbCount = $stockCount - $count;
        $rmbOutArr = ['count' => $rmbCount];
        if($rmbCount === 0) {
//            $this->rawMaterialBalanceModel->delete($request->raw_material_id, $from_department_id, $request->set_id);
            $this->rawMaterialBalanceModel->deleteBySetid($raw_material_id, $from_department_id, $from_set_id);
        } elseif ($request->stockCount === 0) {
            $rmBalanceArr = [
                "raw_material_id"   => $raw_material_id,
                "product_id"        => $request->product_id,
                "count"             => $rmbCount,
                "price"             => $request->price,
                "department_id"     => $department_id,
                "set_id"            => $request->set_id,
            ];
            $this->rawMaterialBalanceModel->add($rmBalanceArr);
        } else {
//            $this->rawMaterialBalanceModel->update($raw_material_id, $from_department_id, $request->set_id, $rmbOutArr);
            $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $from_department_id, $from_set_id, $rmbOutArr);
        }

        $rmbInArr = ['count' => $count];
//        $this->rawMaterialBalanceModel->update($request->raw_material_id, $to_department_id, $request->set_id, $rmbInArr);
        $this->rawMaterialBalanceModel->updateBySetid($raw_material_id, $to_department_id, $set_id, $rmbInArr);

        return $this->rawMaterialDetailsModel->getSetRawMaterials($request->set_id, $request->inout, $department_id);
    }

    public function deleteSemiFinishProductOut(Request $request)
    {
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
//        $rmdCountOut    = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id, $inout_set_id);
//        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterial($raw_material_id, $department_id, $inout_set_id);
        $rmdCountOut = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $department_id, $from_set_id);
        $rawMaterialOut = $this->rawMaterialBalanceModel->getRawMaterialBySetid($raw_material_id, $department_id, $from_set_id);
        if(!is_null($rawMaterialOut)){
            $rmbArr = [
                'count' => $rmdCountOut,
            ];
//            $this->rawMaterialBalanceModel->update($raw_material_id, $department_id, $inout_set_id, $rmbArr);
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
//        $rmdCountIn     = $this->rawMaterialDetailsModel->getRawMaterialCount($raw_material_id, $to_department_id, $inout_set_id);
//        if($rmdCountIn > 0){
//            $rmbArr = ['count' => $rmdCountIn];
//            $this->rawMaterialBalanceModel->update($raw_material_id, $to_department_id, $inout_set_id, $rmbArr);
//        } else {
//            $this->rawMaterialBalanceModel->delete($raw_material_id, $to_department_id, $inout_set_id);
//        }

        return $this->rawMaterialDetailsModel->getSetRawMaterials($set_id, 2, $department_id);
    }

}
