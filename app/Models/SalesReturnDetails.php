<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnDetails extends Model
{
  use HasFactory;

    protected $table = "sales_return_details";

    public $timestamps = false;

    protected $primaryKey = 'sr_details_id';


    protected $fillable = [
        'sr_details_id',
        'sr_id', 
        'le_details_id',
        'le_secondary_details_id',
        'dp_details_id',
        'item_id',
        'item_details_id',
        'sr_details_qty', 
        'sr_qty', 
        'fitting_item', 
        'remark', 
        'status', 
    ];
}