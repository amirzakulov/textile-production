<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDepartment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        "id",
        "product_id",
        "department_id",
    ];
}
