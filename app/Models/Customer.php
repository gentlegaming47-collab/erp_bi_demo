<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = "customers";

    public $timestamps = false;

    protected $fillable = [
        'id', 
        'customer_name',
        'customer_group_id',
        'address',
        'village_id',
        'pincode',
        'mobile_no',
        'email',
        'PAN',
        'gst_code',
        'aadhar_no',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on',
        'customer_code',
        'register_number'
    ];
}
