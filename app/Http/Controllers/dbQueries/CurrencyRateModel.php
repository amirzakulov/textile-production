<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\CurrencyRate;
use Illuminate\Support\Facades\DB;

class CurrencyRateModel extends Controller
{
    public function getCurrencyRates($currency_id){
        return DB::table("currency_rates")
            ->where("currency_id", $currency_id)
            ->orderBY("created_at", "desc")
            ->get();
    }

    public function getCurrencyRate($id){
        return DB::table("currency_rates")
            ->where("id", $id)
            ->first();
    }

    public function getLastCurrencyRate($currency_id)
    {
        return DB::table("currency_rates")
            ->where("currency_id", $currency_id)
            ->latest()
            ->first();
    }

    public function add($arr)
    {
        $currency_rate = CurrencyRate::create($arr);

        return $this->getCurrencyRate($currency_rate->id);
    }

    public function update($id, $arr)
    {
        CurrencyRate::where("id", $id)->update($arr);

        return $this->getCurrencyRate($id);
    }

    public function delete($id)
    {
        return CurrencyRate::where('id', $id)->delete();
    }

}
