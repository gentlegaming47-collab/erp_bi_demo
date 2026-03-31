<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationDetailStock extends Model
{
    use HasFactory;

    protected $table = "location_stock_details";

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'item_details_id',
        'stock_qty',    
        'secondary_stock_qty',    
    ];
}