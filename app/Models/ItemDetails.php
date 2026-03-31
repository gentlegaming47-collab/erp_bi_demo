<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDetails extends Model
{
    use HasFactory;

    protected $table = "item_details";

    public $timestamps = false;

    protected $fillable = [
        'item_details_id',
        'item_id',
        'secondary_qty',
        'secondary_wt_pc',
        'secondary_item_name',
        'status',
    ];
}