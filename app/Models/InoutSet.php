<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InoutSet extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "user_id",
        "total",
        "inout",
        "from_department_id",
        "to_department_id",
        "department_id",
        "isRawMaterial",
        "created_date",
    ];

}
