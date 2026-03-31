<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $table = "dealers";

    public $timestamps = false;

    protected $fillable = [
        'id', 
        'dealer_name',
        'dealer_code',
        'address',
        'village_id',
        'pincode',
        'mobile_no',
        'email',
        'PAN',
        'gst_code',
        'aadhar_no',
        'aggrement_start_date',
        'aggrement_end_date',
        'aggrement_document',
        'cheque_no',
        'status',
        'account_name',
        'bank_name',
        'branch_name',
        'account_no',
        'account_type', 
        'ifsc_code',
        'micr_code',
        'swift_code',
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