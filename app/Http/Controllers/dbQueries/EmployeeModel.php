<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeModel extends Controller
{
    public function getAll(){
        return DB::table("employees as e")
            ->select("e.*", "d.name as department_name")
            ->leftJoin("departments as d", "d.id", "=", "e.department_id")
            ->orderBy('e.last_name')
            ->get();
    }
    public function getActiveEmployees()
    {
        return DB::table("employees as e")
            ->select("e.*", "d.name as department_name")
            ->leftJoin("departments as d", "d.id", "=", "e.department_id")
            ->where("e.active", 1)
            ->orderBy('e.last_name')
            ->get();
    }

    public function getInactiveEmployees()
    {
        return DB::table("employees as e")
            ->select("e.*", "d.name as department_name")
            ->leftJoin("departments as d", "d.id", "=", "e.department_id")
            ->where("e.active", 2)
            ->orderBy('e.last_name')
            ->get();
    }

    public function getEmployee($id){
        return DB::table("employees as e")
            ->select("e.*", "d.name as department_name")
            ->leftJoin("departments as d", "d.id", "=", "e.department_id")
            ->where("e.id", $id)
            ->first();
    }

    public function add($arr)
    {
        $employee = Employee::create($arr);

        return $this->getEmployee($employee->id);
    }

    public function update($id, $arr)
    {
        Employee::where('id', $id)->update($arr);

        return $this->getEmployee($id);
    }

    public function delete($id)
    {
        return Employee::where('id', $id)->delete();
    }

}
