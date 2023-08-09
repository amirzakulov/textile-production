<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\CurrencyModel;
use App\Http\Controllers\dbQueries\CurrencyRateModel;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    private $currencyModel;
    private $currencyRateModel;

    public function __construct()
    {
        $this->currencyModel = new CurrencyModel();
        $this->currencyRateModel = new CurrencyRateModel();
    }

    public function getCurrencies()
    {
        return $this->currencyModel->getCurrencies();
    }

    public function getCurrencyRates($currency_id)
    {
        return $this->currencyRateModel->getCurrencyRates($currency_id);
    }

    public function getLastCurrencyRate($currency_id)
    {
        return $this->currencyRateModel->getLastCurrencyRate($currency_id);
    }

    /** ****************************** CRUD Actions *********************************** **/

    public function addCurrencyRate(Request $request)
    {
        $this->validate($request, [
            'rate' => "required",
        ]);

        $arr = [
            'currency_id'   => $request->currency_id,
            'rate'          => $request->rate
        ];

        return $this->currencyRateModel->add($arr);
    }

    public function updateCurrencyRate(Request $request)
    {
        $this->validate($request, [
            'rate' => "required",
        ]);

        $arr = ['rate' => $request->rate];

        return $this->currencyRateModel->update($request->id, $arr);
    }

    public function deleteCurrencyRate(Request $request)
    {
        $this->validate($request, [
            'id' => "required",
        ]);

        return $this->currencyRateModel->delete($request->id);
    }

}
