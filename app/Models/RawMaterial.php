<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        "id",
        "product_id",
        "price",
        "price_value",
        "currency_id",
        "currency_rate",
        "set_id",
        "set_name",
        "department_id",
    ];
}
