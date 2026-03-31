<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNMaterailSecondaryDetails extends Model
{
    use HasFactory;

      protected $table = "grn_secondary_details";

    public $timestamps = false;

    protected $fillable = [
        'grn_secondary_details_id', 
        'grn_details_id',
        'main_grn_id',
        'le_secondary_details_id',
        'dp_details_id',
        'le_details_id',
        'item_id',       
        'item_details_id',       
        'grn_qty',       
        'rate_per_unit',       
        'amount',       
        'remarks',       
        'mismatch_qty',
        'status',        
    ];
}