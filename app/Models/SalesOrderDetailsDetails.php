<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetailsDetails extends Model
{
    use HasFactory;

    public $timestamps = false;

    public $table = 'sales_order_detail_details';

    protected $primaryKey = 'sod_details_id';
    
    protected $fillable = [
        'so_details_id',
        'item_id',
        'rate_per_unit',
        'so_qty',
        'so_amount',
        'status',
    ];
}