<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingEntry extends Model
{
    use HasFactory;

    protected $primaryKey = 'le_id';

    protected $table = "loading_entry";

    public $timestamps = false;

    protected $fillable = [
        'le_id', 
        'current_location_id',
        'dp_id',
        'dp_number',
        'vehicle_no',
        'transporter_id',
        'loading_by',
        'driver_name',
        'driver_mobile_no',
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