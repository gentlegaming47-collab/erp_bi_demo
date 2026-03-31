<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class POShortClose extends Model
{
    use HasFactory;

    public $table = 'purchase_order_short_close';

    public $timestamps = false;

    protected $primaryKey = 'posc_id';

    protected $fillable = [
        'po_details_id',
        'current_location_id',
        'sc_date',
        'sc_qty',
        'reason',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on',        
    ];
}
