<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingEntrySecondaryDetails extends Model
{
    use HasFactory;


     public $timestamps = false;
    
    protected $primaryKey = 'le_secondary_details_id';

    public $table = 'loading_entry_secondary_details';

    protected $fillable = [
        'le_details_id',
        'dp_secondary_details_id',
        'dp_details_id',
        'item_id',
        'item_details_id',
        'plan_qty',
        'status',
    ];
}