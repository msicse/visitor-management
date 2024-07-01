<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;
    protected $fillable = [
        "employee_id",
        "department_id",
        "visitor_card_id",
        "name",
        "visitor_type",
        "organization",
        "phone",
        "email",
        "reason",
        "address",
        "remarks",
        "image",
        "checkout",
        "in_time",
        "out_time",
        

    ];
    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function guests(){
        return $this->hasMany(VisitorGuest::class);
    }


}
