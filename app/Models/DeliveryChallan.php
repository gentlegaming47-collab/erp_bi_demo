<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{
    use HasFactory;

    protected $primaryKey = 'dc_id';

    public $timestamp = false;

    public $table = 'delivery_challan';

    protected $fillable = [
        'dc_from_id_fix',
        'dc_from_value_fix',
        'current_location_id',
        'dc_sequence',
        'dc_number',
        'dc_date',
        'customer',
        'to_location_id',
        'transporter_id',
        'vehicle_no',
        'lr_no_and_date',
        'invoice_number',
        'invoice_date',
        'special_notes',
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
