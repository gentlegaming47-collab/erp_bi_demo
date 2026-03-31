<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemStockTransferDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'ist_details_id';

    protected $table = "item_stock_transfer_details";

    public $timestamps = false;

    protected $fillable = [
        'ist_details_id', 
        'ist_id',
        'item_details_id',
        'item_stock_qty',
        'status',
        'stock_transfer_qty'
    ];
}