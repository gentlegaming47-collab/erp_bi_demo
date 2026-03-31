<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    public $table = 'stock_log';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'section',
        'operation',
        'location_id',
        'pre_item_id',
        'current_item_id',
        'pre_qty',
        'current_qty',
        'form_id',
        'created_by_user_id',
        'created_on'
    ];
}