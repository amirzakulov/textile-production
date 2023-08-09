<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\DepartmentsModel;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    private $departmentsModel;

    public function __construct()
    {
        $this->departmentsModel = new DepartmentsModel();
    }

    public function getDepartments()
    {
        return $this->departmentsModel->getDepartments();
    }

    public function getDepartmentsByType($type) {

        return $this->departmentsModel->getDepartmentsByType($type);
    }

    public function getDepartmentsByTypes(Request $request) {
        $types = $request->types;
        return $this->departmentsModel->getDepartmentsByTypes($types);
    }

}
