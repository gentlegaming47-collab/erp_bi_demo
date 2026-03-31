<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use HasFactory;

    protected $table = "sales_order";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'so_from_id_fix',
        'so_from_value_fix',
        'so_type_id_fix',
        'so_type_value_fix',
        'so_sequence',
        'so_number',
        'so_date',
        'customer_group_id',
        'customer_name',
        'dealer_id',
        'customer_reg_no',
        'customer_village',
        'current_location_id',
        'customer_pincode',
        'customer_taluka',
        'to_location_id',        
        'customer_district_id',        
        'mobile_no',        
        'area',        
        'ship_to',        
        'special_notes',
        'file_upload',
        'total_qty',
        'total_amount',
        'basic_amount',
        'less_discount_percentage',
        'less_discount_amount',
        'secondary_transport',
        'sharing_head_unit_cost',
        'installation_charge',
        'gst_type_fix_id',
        'sgst_percentage',
        'sgst_amount',
        'cgst_percentage',
        'cgst_amount',
        'igst_percentage',
        'igst_amount',
        'net_amount',
        'round_off_val',
        'mis_category_id',
        'year_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
    ];
}