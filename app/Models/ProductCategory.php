<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        "id",
        "name",
    ];
}
