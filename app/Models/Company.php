<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = "companies";

    /**
     * disable laravel default timestamps field
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'company_name',
        'company_code',
        'address',
        'city',
        'pin_code',
        'state',
        'state_code',
        'country',
        'phone_no',
        'email',
        'web_address',
        'gstin',
        'pan',
        'reverse_charge',
        'bank_name',
        'branch_name',
        'account_no',
        'account_type',
        'ifsc_code',
        'company_logo',
        'other_logo_1',
        'other_logo_2',
        'other_logo_3',
        'created_by',
        'last_by',
        'locked_by',
        'created_on',
        'last_on',
        'locked_on'
    ];
}
