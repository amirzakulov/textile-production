<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\ProductCategoriesModel;

class ProductCategoriesController
{
    private $categoryModel;

    public function __construct(){
        $this->categoryModel = new ProductCategoriesModel();
    }

    public function getCategories(){
        return $this->categoryModel->getCategories();
    }



}
