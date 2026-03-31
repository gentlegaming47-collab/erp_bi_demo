<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = "locations";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'location_name',
        'type',
        'customer_id',
        'mfg_process',
        'header_print',
        'village_id',
        'location_code',
        'status',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];
}
