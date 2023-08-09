<?php

namespace App\Http\Controllers\reports;

use App\Http\Controllers\Controller;
use App\Http\Controllers\dbQueries\CommunalExpensesModel;
use App\Http\Controllers\dbQueries\CommunalTypesModel;
use App\Http\Controllers\dbQueries\ExpensesModel;
use App\Http\Controllers\dbQueries\ExpenseTypesModel;
use Illuminate\Http\Request;

class ExpensesReportController extends Controller
{
    private $expenseTypesModel;
    private $expensesModel;
    private $communalExpensesModel;
    private $communalTypesModel;

    public function __construct(){
        $this->expenseTypesModel = new ExpenseTypesModel();
        $this->expensesModel = new ExpensesModel();
        $this->communalExpensesModel = new CommunalExpensesModel();
        $this->communalTypesModel = new CommunalTypesModel();
    }


    public function getMonthlyCommunalExpensesTotal(Request $request){
        $year = $request->year;
        $month = $request->month;
        $communalExpenses = $this->communalExpensesModel->getMonthlyExpense($year, $month);

        $total = 0;
        foreach($communalExpenses as $expense){
            $total += ($expense->payment);
        }

        return $total;
    }

    public function getMonthlyOtherExpensesTotal(Request $request){
        $year = $request->year;
        $month = $request->month;
        $expenses = $this->expensesModel->getMonthlyExpenses($year, $month);

        $total = 0;
        foreach($expenses as $expense){
            $total += $expense->amount;
        }

        return $total;
    }

    public function communalExpenses(Request $request)
    {
        $formData  = $request->formData;
        $startDate = $formData["startDate"];
        $endDate   = $formData["endDate"];

        $types = $this->communalTypesModel->getTypes();
        $expenses = $this->communalExpensesModel->communalReport($startDate, $endDate);

        foreach ($types as $type) {
            $type->active_amount = 0;
            $type->payment = 0;
            foreach ($expenses as $expense) {
                if($type->id == $expense->communal_type_id) {
                    $type->active_amount = $expense->active_amount;
                    $type->payment = $expense->payment;
                }
            }
        }

        return $types;

    }

    public function expenses(Request $request)
    {
        $formData  = $request->formData;
        $startDate = $formData["startDate"];
        $endDate   = $formData["endDate"];

        $types = $this->expenseTypesModel->getExpenseTypes();
        $expenses = $this->expensesModel->expensesByType($startDate, $endDate);

        foreach ($types as $type) {
            $type->amount = 0;
            foreach ($expenses as $expense) {
                if($type->id == $expense->expense_type_id) {
                    $type->amount = $expense->amount;
                }
            }
        }

        return $types;
    }

}
