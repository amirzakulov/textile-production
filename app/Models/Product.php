<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "name",
        "code",
        "color",
        "country",
        "measurement",
        "picture",
        "description",
        "min_count",
        "fashion_id",
        "department_id",
        "isRawMaterial",
        "category_id",
    ];
}
