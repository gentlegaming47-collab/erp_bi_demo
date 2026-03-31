<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionDetails extends Model
{
    use HasFactory;

    protected $table = "purchase_requisition_details";

    public $timestamps =  false;

    protected $primaryKey = 'pr_details_id';

    protected $fillable = [
        'pr_details_id ',
        'pr_id',
        'item_id',
        'req_qty',
        'supplier_id',
        'mr_details_id',
        'rate_per_unit',
        'remarks',
        'status'
    ];
}