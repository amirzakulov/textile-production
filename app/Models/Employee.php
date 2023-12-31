<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "first_name",
        "last_name",
        "middle_name",
        "department_id",
        "phone",
        "address",
        "description",
        "type",
        "hired_date",
        "fired_date",
    ];
}
