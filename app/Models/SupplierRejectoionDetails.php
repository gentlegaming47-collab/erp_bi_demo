<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRejectoionDetails extends Model
{
    use HasFactory;

    
    public $table = 'supplier_rejection_challan_details';

    public $timestamps = false;

    protected $primaryKey = 'src_details_id';

    protected $fillable = [
        'src_details_id',
        'src_id',
        'item_id',
        'challan_qty',
        'remarks',        
        'qc_id',
    ];

}