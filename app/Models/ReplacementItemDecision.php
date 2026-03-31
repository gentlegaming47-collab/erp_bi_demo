<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplacementItemDecision extends Model
{
    use HasFactory;

    protected $table = "replacement_item_decision";

    public $timestamps = false;

    protected $primaryKey = 'replacement_id';

    protected $fillable = [
        'replacement_id',
        'replacement_type_id_fix',
        'replacement_type_value_fix',      
        'current_location_id',
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