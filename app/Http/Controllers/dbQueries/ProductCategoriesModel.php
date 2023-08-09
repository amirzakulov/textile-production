<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ProductCategoriesModel extends Controller
{
    public function getCategories()
    {
        return DB::table("product_categories")
            ->orderBy("name")
            ->get();
    }

    public function getCategory($id)
    {
        return DB::table("product_categories")
            ->where("id", $id)
            ->first();
    }
}
