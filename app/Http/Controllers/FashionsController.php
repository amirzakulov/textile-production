<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\FashionCategoriesModel;
use App\Http\Controllers\dbQueries\FashionsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FashionsController extends Controller
{
    private $fashionsModel;
    private $fashionCategoriesModel;

    public function __construct()
    {
        $this->fashionsModel            = new FashionsModel();
        $this->fashionCategoriesModel   = new FashionCategoriesModel();
    }

    public function getFashions()
    {
        return $this->fashionsModel->getFashions();
    }

    public function getFashion($id)
    {
        return $this->fashionsModel->getFashion($id);
    }

    public function addFashion(Request $request)
    {
        $this->validate($request, [
            'code' => "required",
            'name' => "required",
        ]);

        $arr = [
                'code'                  => $request->code,
                'name'                  => $request->name,
                'fashion_category_id'   => $request->fashion_category_id,
                'description'           => $request->description,
            ];

        return $this->fashionsModel->addFashion($arr);

    }

    public function editFashion(Request $request)
    {
        $this->validate($request, [
            'code' => "required",
            'name' => "required",
            'fashion_category_id' => "required",
        ]);

        $arr = [
            'code'                  => $request->code,
            'name'                  => $request->name,
            'fashion_category_id'   => $request->fashion_category_id,
            'description'           => $request->description,
        ];

        return $this->fashionsModel->editFashion($request->id, $arr);
    }

    public function deleteFashion(Request $request)
    {
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->fashionsModel->deleteFashion($request->id);
    }

    public function getCategories()
    {
        return $this->fashionCategoriesModel->getCategories();
    }
}
