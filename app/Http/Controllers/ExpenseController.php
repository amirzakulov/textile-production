<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\dbQueries\ExpenseTypesModel;
use App\Http\Controllers\dbQueries\ExpensesModel;
use App\Http\Controllers\dbQueries\CommunalExpensesModel;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    private $expenseTypesModel;
    private $expensesModel;
    private $communalExpensesModel;

    public function __construct()
    {
        $this->expenseTypesModel = new ExpenseTypesModel();
        $this->expensesModel = new ExpensesModel();
        $this->communalExpensesModel = new CommunalExpensesModel();

    }

    public function getMonthlyCommunalExpensesTotal($year, $month){
        $communalExpenses = $this->communalExpensesModel->getMonthlyExpense($year, $month);

        $total = 0;
        foreach($communalExpenses as $expense){
            $total += ($expense->payment);
        }

        return $total;
    }

    public function getMonthlyExpensesTotal($year, $month){
        $expenses = $this->expensesModel->getMonthlyExpenses($year, $month);

        $total = 0;
        foreach($expenses as $expense){
            $total += $expense->amount;
        }

        return $total;
    }

    public function expensesTotal(Request $request) {
        $year       = $request->selectedYear;
        $monthes    = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $expenses   = array(
            array('Коммунал тўловлар'),
            array('Қўшимча харажатлар'),
        );

        foreach($monthes as $month){

            $communalExpense = $this->getMonthlyCommunalExpensesTotal($year, $month);
            array_push($expenses[0], $communalExpense);

            $otherExpense   = $this->getMonthlyExpensesTotal($year, $month);
            array_push($expenses[1], $otherExpense);

        }

        return $expenses;
    }

    public function getExpenseTypes(){
        return $this->expenseTypesModel->getExpenseTypes();
    }

    public function editExpenseType(Request $request){
       $this->validate($request, [
            'name' => "required",
        ]);

        $id = $request->id;
        $arr = ["name" => $request->name];
        $this->expenseTypesModel->update($id, $arr);

        return response()->json($request);
    }

    public function addExpenseType(Request $request){
        $this->validate($request, [
            'name' => "required",
        ]);

        $arr = ["name" => $request->name];
        return $this->expenseTypesModel->add($arr);
    }


    public function deleteExpenseType(Request $request){
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->expenseTypesModel->delete($request->id);
    }

    /**
     * Xarajatlar
     * **/
    public function getExpenses(){
        return $this->expensesModel->getExpenses();
    }

    public function addExpense(Request $request) {
        $this->validate($request, [
            'expense_type_id'   => "required",
            'amount'            => "required",
            'date'              => "required",
        ]);

        $arr = [
            "expense_type_id"   => $request->expense_type_id,
            "amount"            => $request->amount,
            "description"       => $request->description,
            "date"              => $request->date,
        ];

        $res        = $this->expensesModel->add($arr);
        $expense    = $this->expensesModel->getExpense($res->id);

        return response()->json($expense);
    }

    public function editExpense(Request $request){
        $this->validate($request, [
            'expense_type_id'   => "required",
            'amount'            => "required",
            'date'              => "required",
        ]);

        $id = $request->id;
        $arr = [
            "expense_type_id"   => $request->expense_type_id,
            "amount"            => $request->amount,
            "description"       => $request->description,
            "date"              => $request->date,
        ];

        $res        = $this->expensesModel->update($id, $arr);
        $expense    = $this->expensesModel->getExpense($id);


        return response()->json($expense);
    }

    public function deleteExpense(Request $request){
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->expensesModel->delete($request->id);
    }
}
