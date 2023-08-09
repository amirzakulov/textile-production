<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialDetail extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $fillable = [
        "id",
        "raw_material_id",
        "count",
        "inout",
        "user_id",
        "product_id",
        "set_id",
        "from_set_id",
        "from_department_id",
        "to_department_id",
        "department_id",
        "finish_raw_material_id",
        "finish_product_id",
        "created_date",
    ];
}
