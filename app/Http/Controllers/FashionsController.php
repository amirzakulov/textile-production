<?php

namespace App\Http\Controllers;

use App\Http\Controllers\dbQueries\FashionCategoriesModel;
use App\Http\Controllers\dbQueries\FashionsDetailsModel;
use App\Http\Controllers\dbQueries\FashionsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FashionsController extends Controller
{
    private $fashionsModel;
    private $fashionCategoriesModel;
    private $fashionDetailsModel;

    public function __construct()
    {
        $this->fashionsModel            = new FashionsModel();
        $this->fashionCategoriesModel   = new FashionCategoriesModel();
        $this->fashionDetailsModel      = new FashionsDetailsModel();
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

    public function getFashionDetails($fashion_id)
    {
        return $this->fashionDetailsModel->getFashionDetails($fashion_id);
    }

    public function addFashionDetail(Request $request)
    {
        $this->validate($request, [
            'product_id' => "required",
            'count'      => "required",
        ]);

        $arr = [
            "fashion_id" => $request->fashion_id,
            "product_id" => $request->product_id,
            "count"      => $request->count,
            "price"      => $request->price,
        ];

        return $this->fashionDetailsModel->add($arr);
    }

    public function editFashionDetail(Request $request)
    {
        $this->validate($request, [
            'id'        => "required",
        ]);

        $arr = [
            "product_id" => $request->product_id,
            "count" => $request->count,
            "price" => $request->price,
        ];

        return $this->fashionDetailsModel->update($request->id, $arr);
    }

    public function deleteFashionDetail(Request $request)
    {
        $this->validate($request, [
            'id'        => "required",
        ]);

        return $this->fashionDetailsModel->delete($request->id);
    }
}
