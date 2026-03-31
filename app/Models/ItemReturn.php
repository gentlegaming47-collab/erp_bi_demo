<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReturn extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_return_id';

    protected $table = "item_return";

    public $timestamps = false;

    protected $fillable = [
        'item_return_id',       
        'current_location_id',
        'return_sequence',
        'return_number',
        'return_date',        
        'supplier_id',        
        'issue_no',        
        'total_qty',
        'special_notes',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
    ];
}