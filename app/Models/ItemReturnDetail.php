<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReturnDetail extends Model
{
    use HasFactory;

    protected $table = "item_return_details";

    public $timestamps = false;

    protected $fillable = [
        'item_return_details_id',       
        'item_return_id',
        'item_issue_details_id',
        'item_id',
        'item_details_id',
        'return_qty',        
        'remarks', 
        'status'        
    ];
}