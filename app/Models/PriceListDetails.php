<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceListDetails extends Model
{
    use HasFactory;

    protected $table = "price_list_details";

    public $timestamps =  false;

    protected $fillable = [
        'pl_id',
        'item_id',
        'sales_rate',
        'customer_group_id',
        'status'
    ];
}