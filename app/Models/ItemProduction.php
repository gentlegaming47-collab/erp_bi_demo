<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemProduction extends Model
{
    use HasFactory;

    protected $primaryKey = 'ip_id';

    public $timestamps = false;

    public $table = 'item_production';

    public $fillable = [
        'current_location_id',
        'ip_sequence',
        'ip_number',
        'ip_date',
        'total_qty',
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
