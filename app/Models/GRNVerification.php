<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNVerification extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'grn_verification';

    protected $primaryKey = 'gv_id';

    protected $fillable = [
        'grn_details_id',
        'gv_date',
        'grn_details_id',
        'grn_secondary_details_id',
        'item_details_id',
        'item_id',
        'mismatch_qty',
        'gv_reason',
        'current_location_id',
        'to_location_id',
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