<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSOShortClose extends Model
{
    use HasFactory;

      public $table = 'transaction_so_short_close';

    public $timestamps = false;

    protected $primaryKey = 'tr_sosc_id';

    protected $fillable = [
        'so_details_id',
        'tr_sc_date',
        'tr_sc_qty',
        'reason',
        'current_location_id',
        'company_id',
        'year_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on',        
    ];
}