<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $table = "quotation";

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = [

        'quot_sequence',
        'quot_number',
        'quot_date',
        'customer_group_id',
        'customer_name',
        'dealer_id',
        'quot_village_id',
        'quot_taluka_id',
        'quot_district_id',        
        'current_location_id',
        'mis_category_id',
        'pincode',
        'mobile_no',        
        'special_notes',
        'total_qty',
        'total_amount',
        'basic_amount',
        'less_discount_percentage',
        'less_discount_amount',
        'secondary_transport',
        'gst_type_fix_id',
        'sgst_percentage',
        'sgst_amount',
        'cgst_percentage',
        'cgst_amount',
        'igst_percentage',
        'igst_amount',
        'net_amount',
        'round_off_val',
        'year_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on'
    ];
}
