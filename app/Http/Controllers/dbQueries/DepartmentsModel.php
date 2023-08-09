<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DepartmentsModel extends Controller
{
    public function getDepartments()
    {
        return DB::table("departments")
            ->orderBy("name")
            ->get();
    }

    public function getDepartment($id)
    {
        return DB::table("departments")
            ->where("id", $id)
            ->first();
    }

    public function getDepartmentsByType($type)
    {
        return DB::table("departments")
            ->where("type",  $type)
            ->orderBy("sort")
            ->get();
    }

    public function getDepartmentsByTypes($types)
    {
        return DB::table("departments")
            ->whereIn("type",  $types)
            ->orderBy("sort")
            ->get();
    }
}
