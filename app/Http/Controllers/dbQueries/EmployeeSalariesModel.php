<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dbqueries\EmployeeModel;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeSalariesModel extends Controller
{
    private $employeeModel;

    public function __construct(){
        $this->employeeModel = new EmployeeModel();
    }

    public function getPaidEmployees($year){
        return DB::table("employee_salaries as s")
            ->select("s.*", "e.first_name", "e.last_name", "e.middle_name", "e.code")
            ->leftJoin("employees as e", "e.id", "=", "s.employee_id")
            ->where("s.year", $year)
            ->orderBy("e.last_name")
            ->get();
    }

    public function getPaidEmployee($year, $employee_id){
        return DB::table("employee_salaries as s")
            ->select("s.*", "e.first_name", "e.last_name", "e.middle_name", "e.code")
            ->leftJoin("employees as e", "e.id", "=", "s.employee_id")
            ->where("s.year", $year)
            ->where("s.employee_id", $employee_id)
            ->first();
    }

    public function getMonthlySalaryTotal($year, $month){
        return DB::table("employee_salaries as s")
            ->where("s.year", $year)
            ->where("s.month", $month)
            ->groupBy("s.year")
            ->groupBy("s.month")
            ->sum("s.amount");
    }

    public function salaryTotal($startDate, $endDate){
        return DB::table("employee_salaries as s")
            ->where("s.year", ">=", date("Y", strtotime($startDate)))
            ->where("s.year", "<=", date("Y", strtotime($endDate)))
            ->where("s.month", ">=", date("n", strtotime($startDate)))
            ->where("s.month", "<=", date("n", strtotime($endDate)))
            ->groupBy("s.year")
            ->groupBy("s.month")
            ->sum("s.amount");
    }

    /** *************************** ACTIONS *************************** **/

    public function addSalary($arr)
    {
        $salary = EmployeeSalary::create($arr);

        return $this->getPaidEmployee($arr['year'], $salary->employee_id);
    }
}
