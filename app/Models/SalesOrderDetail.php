<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesOrderDetail extends Model
{
    use HasFactory;

    protected $table = "sales_order_details";

    /**
     * disable laravel default timestamps field
     */
    public $timestamps = false;
    protected $primaryKey = 'so_details_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'so_id',
        'item_id',
        'fitting_item',
        'secondary_unit',
        'allow_partial_dispatch',
        // 'production_assembly',
        'so_qty',
        'rate_per_unit',
        'so_amount',       
        'discount',       
        'mr_details_id',
        'remarks',
        'status',       
    ];
}