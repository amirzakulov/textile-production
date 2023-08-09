<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialBalance extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "raw_material_id",
        "product_id",
        "set_id",
        "set_name",
        "count",
        "price",
        "dollar_rate",
        "department_id",
    ];

}
