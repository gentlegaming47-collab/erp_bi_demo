<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SOMappingDetails extends Model
{
    use HasFactory;

    protected $table = "so_mapping_details";

    public $timestamps = false;

    protected $primaryKey = 'so_mapping_details_id';


    protected $fillable = [
        'so_mapping_details_id',
        'mapping_id', 
        'map_qty',
        'so_details_id',
        'cre_detail_id', 
        'item_details_id', 
        'item_detail_qty', 
    ];
}