<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNMaterialDetails extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    public $table = 'material_receipt_grn_details';

    protected $primaryKey = 'grn_details_id';

    protected $fillable = [
        'grn_id',
        'po_details_id',
        'item_id',
        'grn_qty',
        'rate_per_unit',
        'amount',
        'remarks',
        'dc_details_id',       
        'le_details_id',       
        'is_approved',   
        'qc_required',    
        'service_item',    
        'mismatch_qty',    
        'grn_details_type',    
        'status',    
    ];
}