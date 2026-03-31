<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchPlanDetailsDetails extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $primaryKey = 'dpd_details_id';

    public $table = 'dispatch_plan_details_details';

    protected $fillable = [
        'dp_details_id',
        'so_details_detail_id',
        'item_id',
        'plan_qty',
        'status',
    ];
}