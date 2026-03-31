<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetails extends Model
{
    use HasFactory;

    protected $table = "purchase_order_details";

    public $timestamps =  false;
    
    protected $primaryKey = 'po_details_id';    


    protected $fillable = [
       'po_details_id',
       'po_id',
       'item_id',
       'po_qty',
       'rate_per_unit',
       'discount',
       'amount',
       'del_date',
       'remarks',
       'pr_details_id'
    ];

}