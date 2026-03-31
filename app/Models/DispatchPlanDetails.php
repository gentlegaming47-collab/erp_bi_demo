<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchPlanDetails extends Model
{
    use HasFactory;

    protected $table = "dispatch_plan_details";

    public $timestamps = false;

    protected $fillable = [
        'dp_details_id', 
        'dp_id',
        'so_details_id',
        'item_id',
        'plan_qty',
        'fitting_item',
        'secondary_unit',
        'allow_partial_dispatch',
        'so_from_value_fix',
        'wt_pc',
        // 'production_assembly',
        'status',
    ];
}