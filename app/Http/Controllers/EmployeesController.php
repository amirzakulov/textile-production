<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\EmployeeModel;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    private $employeesModel;

    public function __construct()
    {
        $this->employeesModel = new EmployeeModel();
    }

    public function getAll()
    {
        return $this->employeesModel->getAll();
    }
    public function getActiveEmployees()
    {
        return $this->employeesModel->getActiveEmployees();
    }

    public function getInactiveEmployees()
    {
        return $this->employeesModel->getInactiveEmployees();
    }

    public function addEmployee(Request $request)
    {
        $this->validate($request, [
            'first_name'    => "required",
            'last_name'     => "required",
            'department_id' => "required",
            'type'          => "required",
            'hired_date'    => "required",
        ]);

        $arr = [
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'middle_name'   => $request->middle_name,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'department_id' => $request->department_id,
            'type'          => $request->type,
            'hired_date'    => date("Y-m-d", strtotime($request->hired_date)),
            'description'   => $request->description,
        ];

        return $this->employeesModel->add($arr);
    }

    public function editEmployee(Request $request)
    {
        $this->validate($request, [
            'first_name'    => "required",
            'last_name'     => "required",
            'department_id' => "required",
            'type'          => "required",
        ]);

        $arr = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'middle_name'   => $request->middle_name,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'department_id' => $request->department_id,
                'type'          => $request->type,
                'description'   => $request->description,
        ];

        return $this->employeesModel->update($request->id, $arr);
    }

    public function changeEmploymentStatus(Request $request){
        $this->validate($request, [
            'id'         => "required",
            'hired_date' => "required",
            'fired_date' => "required",
        ]);

        $arr = [
            'hired_date'    => date("Y-m-d", strtotime($request->hired_date)),
            'fired_date'    => date("Y-m-d", strtotime($request->fired_date)),
            'active'        => $request->active,
        ];

        return $this->employeesModel->update($request->id, $arr);
    }

    public function deleteEmployee(Request $request)
    {
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->employeesModel->delete($request->id);
    }

}
