<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SOMapping extends Model
{
    use HasFactory;

    protected $table = "so_mapping";

    public $timestamps = false;

    protected $primaryKey = 'mapping_id';


    protected $fillable = [
        'mapping_id', 
        'so_mapping_sequence',
        'so_mapping_number',
        'mapping_date',
        'customer_name',
        'item_id',
        'item_details_id',
        'special_notes',
        'current_location_id',       
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];
}