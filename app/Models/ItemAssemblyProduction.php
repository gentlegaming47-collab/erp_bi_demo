<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAssemblyProduction extends Model
{
    use HasFactory;

    protected $primaryKey = 'iap_id';

    public $timestamps = false;

    public $table = 'item_assembly_production';

    public $fillable = [
        'iap_id',
        'current_location_id',
        'iap_sequence',
        'iap_number',
        'iap_date',
        'item_id',
        'item_details_id',
        'assembly_qty',
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