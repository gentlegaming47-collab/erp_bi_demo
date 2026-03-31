<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRawMaterialMappingDetail extends Model
{
    use HasFactory;

    protected $table = "item_raw_material_mapping_details";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'item_id',
        'item_details_id',
        'raw_material_id',
        'raw_material_qty',
        'status',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];
}