<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunalExpense extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "device_id",
        "communal_type_id",
        "active_value",
        "active_price",
        "active_amount",
        "reactive_value",
        "reactive_price",
        "reactive_amount",
        "payment",
        "date",
    ];
}
