<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemProductionDetail extends Model
{
    use HasFactory;

    public $table = 'item_production_details';

    protected $primaryKey = 'ip_details_id';

    public $timestamps = false;

    protected $fillable = [
        'ip_id',
        'item_id',
        'production_qty',
        'remarks',
    ];
}
