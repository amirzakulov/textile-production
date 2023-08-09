<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\dbQueries\CommunalTypesModel;
use App\Http\Controllers\dbQueries\CommunalDevicesModel;
use App\Http\Controllers\dbQueries\CommunalExpensesModel;
use Illuminate\Support\Facades\DB;

class CommunalExpensesController extends Controller
{
    private $communalTypesModel;
    private $communalDevicesModel;
    private $communalExpensesModel;

    public function __construct()
    {
        $this->communalTypesModel = new CommunalTypesModel();
        $this->communalDevicesModel = new communalDevicesModel();
        $this->communalExpensesModel = new CommunalExpensesModel();
    }

    public function getTypes(){
        return $this->communalTypesModel->getTypes();
    }

    public function getType($communal_type_id){
        return $this->communalTypesModel->getType($communal_type_id);
    }

    public function getCommunalDevices($communal_type_id){
        return $this->communalDevicesModel->getDevices($communal_type_id);
    }

    public function editDevice(Request $request){

        $this->validate($request, [
            'name' => "required",
        ]);

        $id = $request->id;
        $arr = ["name" => $request->name];
        $this->communalDevicesModel->update($id, $arr);

        return response()->json($request);

    }

    public function addDevice(Request $request){

        $this->validate($request, [
            'name' => "required",
        ]);

        $arr = [
            "name" => $request->name,
            "communal_type_id" => $request->communal_type_id,
        ];

        $res = $this->communalDevicesModel->addDevice($arr);
        $device = $this->communalDevicesModel->getDevice($res->id);

        return response()->json($device);

    }

    public function deleteDevice(Request $request){
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->communalDevicesModel->deleteDevice($request->id);
    }

    public function getDevicesPayments($communal_type_id, $device_id = null){
        $payments = $this->communalExpensesModel->getExpenses($communal_type_id);

        return response()->json($payments);
    }

    public function addPayment(Request $request){
        $this->validate($request, [
            'device_id'     => "required",
            'communal_type_id'=> "required",
            'active_value'  => "required",
            'active_price'  => "required",
            'payment'       => "required",
            'date'          => "required",
        ]);

        $paymentData = [
            'device_id'     => $request->device_id,
            'communal_type_id'=> $request->communal_type_id,
            'active_value'  => $request->active_value,
            'active_price'  => $request->active_price,
            'active_amount' => $request->active_amount,
            'reactive_value' => $request->reactive_value,
            'reactive_price' => $request->reactive_price,
            'reactive_amount'=> $request->reactive_amount,
            'payment'       => $request->payment,
            'date'          => $request->date,
        ];

        $payment = $this->communalExpensesModel->addPayment($paymentData);

        return $this->communalExpensesModel->getPayment($payment->id);
    }
    public function editPayment(Request $request){
        $this->validate($request, [
            'active_value' => "required",
            'active_price' => "required",
            'payment'       => "required",
            'date'          => "required",
        ]);

        $prev = $this->communalExpensesModel->getPreviousePayment($request->id, $request->communal_type_id, $request->device_id);
        $next = $this->communalExpensesModel->getNextPayment($request->id, $request->communal_type_id, $request->device_id);

        //current payment
        $id = $request->id;
        $prev_active_value     = 0;
        $prev_reactive_value   = 0;
        if(!is_null($prev)) {
            $prev_active_value     = $prev->active_value;
            $prev_reactive_value   = $prev->reactive_value;
        }
        $active_amount   = $request->active_value   - $prev_active_value;
        $reactive_amount = $request->reactive_value - $prev_reactive_value;
        $current = [
            "active_value"   => $request->active_value,
            "active_amount"  => $active_amount,
            "active_price"   => $request->active_price,
            "reactive_value" => $request->reactive_value,
            "reactive_amount"=> $reactive_amount,
            "reactive_price" => $request->reactive_price,
            "payment"        => $request->payment,
            "date"           => $request->date,
        ];

        $this->communalExpensesModel->update($id, $current);

        //next payment
        if(!is_null($next)) {
            $nextPaymentActiveAmount = $next->active_value - $request->active_value;
            $nextPaymentReactiveAmount = $next->reactive_value - $request->reactive_value;
            $this->communalExpensesModel->update($next->id, array("active_amount" => $nextPaymentActiveAmount, "reactive_amount" => $nextPaymentReactiveAmount));
            $next->active_amount = $nextPaymentActiveAmount;
            $next->reactive_amount = $nextPaymentReactiveAmount;
        }

        $current["id"] = $id;

        return array('current' => $current, 'next' => $next);
    }

    public function deletePayment(Request $request){
        $this->validate($request, [
            'id'        => "required",
        ]);

        $current = $this->communalExpensesModel->getPayment($request->id);
        $this->communalExpensesModel->deletePayment($request->id);

        $next = $this->communalExpensesModel->getNextPayment($request->id, $request->communal_type_id, $request->device_id);

        if(!is_null($next)) {
            $amount = $next->active_amount + $current->active_amount;
            $this->communalExpensesModel->update($next->id, array('active_amount' => $amount));
            $next->active_amount = $amount;
        }

        return $next;
    }

    public function lastPayment($communal_type_id, $device_id){
        return $this->communalExpensesModel->lastPayment($communal_type_id, $device_id);
    }

    public function prevNextPayments(Request $request){
        $prev = $this->communalExpensesModel->getPreviousePayment($request->id, $request->communal_type_id, $request->device_id);
        $next = $this->communalExpensesModel->getNextPayment($request->id, $request->communal_type_id, $request->device_id);

        return array("prev" => $prev, "next" => $next);
    }

    public function communalReport(Request $request){

        $expenses = $this->communalExpensesModel->communalReport($request->startDate, $request->endDate);
        $types = $this->communalTypesModel->getTypes();

        foreach($types as $type) {
            $type->amount = 0;
            $type->payment = 0;
            foreach($expenses as $expense) {
                if($type->id == $expense->communal_type_id){
                    $type->amount = $expense->active_amount;
                    $type->payment = $expense->payment;
                }
            }

        }

        return $types;
    }
}
