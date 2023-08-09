<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fashion extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "name",
        "code",
        "fashion_category_id",
        "description",
    ];
}
