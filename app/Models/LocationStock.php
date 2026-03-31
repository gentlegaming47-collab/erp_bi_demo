<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationStock extends Model
{
    use HasFactory;

    protected $table = "location_stock";

    public $timestamps = false;

    protected $fillable = [
        'location_id',
        'item_id',
        'stock_qty',    
    ];
}