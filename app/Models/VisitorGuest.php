<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorGuest extends Model
{
    use HasFactory;
    protected $fillable = [

        "visitor_id",
        "name",
        "organization",
        "phone",
        "email",
        "visitor_card_id",
        "address",
        "reason",
        "remarks",
        "is_checkin",
        "is_checkout",
        "in_time",
        "out_time",
    ];

    

}
