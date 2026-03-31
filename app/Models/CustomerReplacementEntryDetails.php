<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerReplacementEntryDetails extends Model
{
    use HasFactory;

    protected $table = "customer_replacement_entry_details";

    public $timestamps = false;

    protected $fillable = [
        'cre_detail_id', 
        'cre_id',
        'item_id',
        'item_details_id',
        'return_qty',
        'return_details_qty',
        'remark'
    ];
}
