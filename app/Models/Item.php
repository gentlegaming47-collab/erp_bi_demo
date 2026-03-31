<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = "items";

    public $timestamps = false;

    protected $fillable = [
        'id', 
        'item_name',
        'item_group_id',
        'item_sequence',
        'item_code',
        'unit_id',
        'min_stock_qty',
        'max_stock_qty',
        're_order_qty',
        'hsn_code',
        'rate_per_unit',
        'require_raw_material_mapping',
        'fitting_item',
        'print_dispatch_plan',
        'own_manufacturing',
        'dont_allow_req_msl',
        'service_item',
        'status',
        'allow_partial_dispatch',
        'secondary_unit',
        'wt_pc',
        'show_item_in_print',
        'qty',
        'second_unit',
        'qc_required',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];
}