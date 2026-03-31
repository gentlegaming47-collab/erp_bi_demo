<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRShortClose extends Model
{
    use HasFactory;

    public $table = 'purchase_requisition_short_close';

    public $timestamps = false;

    protected $primaryKey = 'prsc_id';

    protected $fillable = [
        'prsc_id',
        'pr_details_id',
        'current_location_id',
        'pr_sc_date',
        'pr_sc_qty',
        'reason',
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
