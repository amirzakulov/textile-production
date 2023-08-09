<?php

use Haruncpi\LaravelIdGenerator\IdGenerator;

if(!function_exists('getMeasurements')){
    function getMeasurements(){
        return [
                ['label' => 'Метр',      'value' => 'м'],
                ['label' => 'Килограмм', 'value' => 'кг'],
                ['label' => 'Дона',      'value' => 'дона'],
            ];
    }
}

if(!function_exists('idGenerate')){
    /**
     * @throws Exception
     */
    function idGenerate($table, $field, $length, $prefix)
    {
        return IdGenerator::generate(['table' => $table, 'field' => $field, 'length' => $length, 'prefix' => $prefix]);
    }
}

if(!function_exists('DateToDateTime')){
    /**
     * @throws Exception
     */
    function dateToDateTime($date)
    {
        $time     = new DateTime();
        $date     = new DateTime($date);
        $dateTime = $date->format('Y-m-d').' '.$time->format('H:i:s');

        return $dateTime;
    }
}
