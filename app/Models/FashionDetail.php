<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FashionDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "fashion_id",
        "product_id",
        "count",
    ];
}
