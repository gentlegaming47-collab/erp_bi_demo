<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationCustomerGroupMapping extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = "location_to_customer_group_mapping";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'location_id',
        'customer_group_id',
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