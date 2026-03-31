<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingEntryDetails extends Model
{
    use HasFactory;

    protected $table = "loading_entry_details";

    public $timestamps = false;

    protected $fillable = [
        'le_details_id', 
        'le_id',
        'dp_details_id',
        'loading_qty',
        'status',       
    ];
}