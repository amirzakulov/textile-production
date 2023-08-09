<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dbQueries\ProductsModel;
use App\Http\Controllers\dbQueries\RawMaterialDetailsModel;
use DateTime;
use Illuminate\Http\Request;

class DepartmentFinishedProductsReportController extends Controller
{
    private $rawMaterialDetailsModel;
    private $productsModel;

    public function __construct(){
        $this->rawMaterialDetailsModel  = new RawMaterialDetailsModel();
        $this->productsModel            = new ProductsModel();
    }


    /** **************************** REPORTS ************************* *
     * @throws \Exception
     */
    public function departmentFinishedProductsReport(Request $request)
    {
        $year           = strval($request->year);
        $month          = strval($request->month);
        $department_id  = $request->department_id;
        $isRawMaterial  = $request->isRawMaterial;
        $product_department_id  = $request->product_department_id;

        $dateString = $year.'-'.$month.'-01';

        $date = new DateTime($dateString);
        $date->modify('first day of this month');
        $first_day_this_month = $date->format('Y-m-d');

        $date->modify('last day of this month');
        $last_day_this_month = $date->format('Y-m-d');

        $date->modify('last day of previous month');
        $last_day_prev_month = $date->format('Y-m-d');

        $balance_this_month     = $this->productsModel->getDepartmentFinishedProductsBalance($last_day_this_month, $department_id, $product_department_id);
        $balance_previous_month = $this->productsModel->getDepartmentFinishedProductsBalance($last_day_prev_month, $department_id, $product_department_id);

        $inProducts             = $this->getInOutProductsByDate(1, $first_day_this_month, $last_day_this_month, $department_id, $isRawMaterial);
        $outProducts            = $this->getInOutProductsByDate(2, $first_day_this_month, $last_day_this_month, $department_id, $isRawMaterial);

        foreach ($balance_this_month as $product_id => $product) {
            $balance_this_month[$product_id]->prev_month_name        = $balance_previous_month[$product_id]->name;
            $balance_this_month[$product_id]->prev_month_measurement = $balance_previous_month[$product_id]->measurement;
            $balance_this_month[$product_id]->prev_month_code        = $balance_previous_month[$product_id]->code;
            $balance_this_month[$product_id]->prev_month_country     = $balance_previous_month[$product_id]->country;
            $balance_this_month[$product_id]->prev_month_price       = $balance_previous_month[$product_id]->price;
            $balance_this_month[$product_id]->prev_month_price_value = $balance_previous_month[$product_id]->price_value;
            $balance_this_month[$product_id]->prev_month_currency_rate = $balance_previous_month[$product_id]->currency_rate;
            $balance_this_month[$product_id]->prev_month_currency_id = $balance_previous_month[$product_id]->currency_id;
            $balance_this_month[$product_id]->prev_month_count       = $balance_previous_month[$product_id]->count;
            $balance_this_month[$product_id]->prev_month_set_name    = $balance_previous_month[$product_id]->set_name;
            $balance_this_month[$product_id]->prev_month_inout       = $balance_previous_month[$product_id]->inout;
            $balance_this_month[$product_id]->prev_month_created_at  = $balance_previous_month[$product_id]->created_at;

            $balance_this_month[$product_id]->inCount          = isset($inProducts[$product_id]) ? $inProducts[$product_id]->count:'';
            $balance_this_month[$product_id]->inPrice          = isset($inProducts[$product_id]) ? $inProducts[$product_id]->price:'';
            $balance_this_month[$product_id]->inPriceValue     = isset($inProducts[$product_id]) ? $inProducts[$product_id]->price_value:'';
            $balance_this_month[$product_id]->inCurrencyRate   = isset($inProducts[$product_id]) ? $inProducts[$product_id]->currency_rate:'';

            $balance_this_month[$product_id]->outCount         = isset($outProducts[$product_id]) ? $outProducts[$product_id]->count:'';
            $balance_this_month[$product_id]->outPrice         = isset($outProducts[$product_id]) ? $outProducts[$product_id]->price:'';
            $balance_this_month[$product_id]->outPriceValue    = isset($outProducts[$product_id]) ? $outProducts[$product_id]->price_value:'';
            $balance_this_month[$product_id]->outCurrencyRate  = isset($outProducts[$product_id]) ? $outProducts[$product_id]->currency_rate:'';

        }

        return $balance_this_month;
    }

    private function getInOutProductsByDate($inout, $first_day_this_month, $last_day_this_month, $department_id, $isRawMaterial)
    {
        $products = $this->rawMaterialDetailsModel->getInOutProductsByDate($inout, $first_day_this_month, $last_day_this_month, $department_id, $isRawMaterial);

        $inout = array();
        foreach ($products as $product) {
            $product->price = intval($product->price);
            $inout[$product->product_id] = $product;
        }

        return $inout;
    }

    public function finishedProductsInoutReport(Request $request)
    {
        $formData       = $request->formData;
        $productionData = $request->productionData;

        $startDate      = $formData["startDate"];
        $endDate        = $formData["endDate"];

        $inout          = $productionData["inout"];
        $department_id  = $productionData["department_id"];
        $isRawMaterial  = 0;

        return $this->rawMaterialDetailsModel->getInOutProductsByDate($inout, $startDate, $endDate, $department_id, $isRawMaterial);
    }

    public function rawMaterialsInoutReport(Request $request)
    {
        $formData       = $request->formData;
        $productionData = $request->productionData;

        $startDate      = $formData["startDate"];
        $endDate        = $formData["endDate"];

        $inout          = $productionData["inout"];
        $department_id  = $productionData["department_id"];
        $isRawMaterial  = 1;

        return $this->rawMaterialDetailsModel->getInOutProductsByDate($inout, $startDate, $endDate, $department_id, $isRawMaterial);
    }


}
