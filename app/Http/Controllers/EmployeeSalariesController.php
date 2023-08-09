<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\EmployeeSalariesModel;
use Illuminate\Http\Request;

class EmployeeSalariesController extends Controller
{
    private $employeeSalariesModel;

    public function __construct(){
        $this->employeeSalariesModel = new EmployeeSalariesModel();
    }
    public function getSalaries(Request $request)
    {
        /* Oylik tulangan ishchilar ruyxati */
        $paidEmployees = $this->employeeSalariesModel->getPaidEmployees($request->year);

        $employeesSalary = array();
        $employees = array();
        $months = array();

        foreach ($paidEmployees as $salary) {

            $employees[$salary->employee_id]["id"]          = $salary->id;
            $employees[$salary->employee_id]["last_name"]   = $salary->last_name;
            $employees[$salary->employee_id]["first_name"]  = $salary->first_name;
            $employees[$salary->employee_id]["middle_name"] = $salary->middle_name;
            $employees[$salary->employee_id]["code"]        = $salary->code;

            for($m = 1; $m <= 12; $m++) {
                $month = $this->getMonthName($m);
                $employees[$salary->employee_id][$month] = '';
            }

        }

        foreach ($paidEmployees as $salary) {
            for($m = 1; $m <= 12; $m++) {
                $month = $this->getMonthName($m); //Oy nomini olvolamiz oy raqami buyicha
                if($salary->month == $m && empty($arr[$salary->employee_id][$month])){
                    $employees[$salary->employee_id][$month] = $salary->amount;
                }

            }
        }

        foreach ($employees as $salary) {
            array_push($employeesSalary, $salary);
        }

        return $employeesSalary;
    }

    public function addSalary(Request $request){
        $this->validate($request, [
            'year'      => 'required',
            'month'     => 'required',
            'employee_id' => 'required',
            'amount'    => 'required',
        ]);

        $arr = [
            'year'      => $request->year,
            'month'     => $request->month,
            'employee_id' => $request->employee_id,
            'amount'    => $request->amount,
        ];

        return $this->employeeSalariesModel->addSalary($arr);

    }

    private function getMonthName($monthNumber) {
        $monthes = array(
            1 => "january",
            2 => "february",
            3 => "march",
            4 => "april",
            5 => "may",
            6 => "june",
            7 => "july",
            8 => "august",
            9 => "september",
            10 => "october",
            11 => "november",
            12 => "december",
        );

        return $monthes[$monthNumber];
    }

    public function getMonthlySalaryTotal(Request $request)
    {
        $year = $request->year;
        $month = $request->month;

        return $this->employeeSalariesModel->getMonthlySalaryTotal($year, $month);
    }

    public function getMonthlyTotal($date){
        $month  = date("n", strtotime($date));
        $year   = date("Y", strtotime($date));

        $salaryData = DB::table("employee_salaries as s")
        ->whereRaw('s.month = ?',[$month])
        ->whereRaw('s.year = ?',[$year])
        ->get();

        $total = 0;
        foreach($salaryData as $salary){
            $total += $salary->amount;
        }

        return $total;

    }

}
