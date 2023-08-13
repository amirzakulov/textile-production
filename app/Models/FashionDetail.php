<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FashionDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        "fashion_id",
        "product_id",
        "count",
        "price",
    ];
}
