<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunalDevice extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        "id",
        "name",
        "communal_type_id"
    ];
}
