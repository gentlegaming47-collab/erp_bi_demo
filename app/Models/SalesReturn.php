<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
   use HasFactory;

    protected $table = "sales_return";

    public $timestamps = false;

    protected $primaryKey = 'sr_id';

    protected $fillable = [
        'sr_id',
        'sr_from_id_fix',
        'sr_from_value_fix',
        'sr_sequence',
        'sr_number',
        'sr_date',
        'customer_name',
        'dp_no_id',
        'transporter_id',
        'vehicle_no',
        'lr_no_date',
        'sp_note',
        'current_location_id',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
    ];
}