<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispatchPlan extends Model
{
    use HasFactory;

    protected $primaryKey = 'dp_id';

    protected $table = "dispatch_plan";

    public $timestamps = false;

    protected $fillable = [
        'current_location_id',
        'dp_id', 
        'dispatch_from_id_fix', 
        'dispatch_from_value_fix', 
        'dp_sequence',
        'dp_number',
        'dp_date',
        'multiple_loading_entry',
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