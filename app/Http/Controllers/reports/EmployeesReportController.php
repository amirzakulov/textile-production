<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dbQueries\EmployeeModel;
use App\Http\Controllers\dbQueries\EmployeeSalariesModel;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;

class EmployeesReportController extends Controller
{
    private $employeesModel;
    private $employeeSalariesModel;

    public function __construct(){
        $this->employeesModel = new EmployeeModel();
        $this->employeeSalariesModel = new EmployeeSalariesModel();
    }

    public function salaryTotal(Request $request)
    {
        $formData  = $request->formData;
        $startDate = $formData["startDate"];
        $endDate   = $formData["endDate"];

        return $this->employeeSalariesModel->salaryTotal($startDate, $endDate);
    }

}
