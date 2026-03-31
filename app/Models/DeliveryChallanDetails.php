<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryChallanDetails extends Model
{
    use HasFactory;

    protected $primaryKey = 'dc_details_id';

    public $timestamp = false;

    public $table = 'delivery_challan_details';

    protected $fillable = [
        'dc_id',
        'item_id',
        'dc_qty',
        'so_detail_id',
    ];

}