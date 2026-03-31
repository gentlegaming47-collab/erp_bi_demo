<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStockTransfer extends Model
{
    use HasFactory;

    protected $primaryKey = 'ist_id';

    protected $table = "item_stock_transfer";

    public $timestamps = false;

    protected $fillable = [
        'ist_id', 
        'ist_sequence',
        'ist_number',
        'ist_date',
        'ist_item_id',
        'ist_item_details_id',
        'current_location_id',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];

   
}