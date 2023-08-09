<?php

namespace App\Http\Controllers\dbQueries;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FashionCategoriesModel extends Controller
{
    public function getCategories(){
        return DB::table("fashion_categories as f")
            ->orderBy('f.sort')
            ->get();
    }

}
