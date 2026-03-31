<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchPlanSecondaryDetails extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $primaryKey = 'dp_secondary_details_id';

    public $table = 'dispatch_plan_secondary_details';

    protected $fillable = [
        'dp_secondary_details_id',
        'dp_details_id',
        'so_details_id',
        'item_id',
        'item_details_id',
        'raw_material_id',
        'plan_qty',
        'status',
    ];
}