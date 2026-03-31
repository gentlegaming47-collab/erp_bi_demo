<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReplacementEntry extends Model
{
    use HasFactory;

    protected $table = "customer_replacement_entry";

    public $timestamps = false;

    protected $primaryKey = 'cre_id';


    protected $fillable = [
        'cre_id', 
        'cre_sequence',
        'cre_number',
        'cre_date',
        'customer_reg_no',
        'rep_customer_id',
        'rep_customer_name',
        'current_location_id',
        'customer_group_id',
        'cre_village',
        'cre_pincode',
        'cre_taluka_id',
        'cre_district_id',
        'special_notes',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];
}