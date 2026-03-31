<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $table = "raw_materials";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'raw_material',
        'raw_material_group_id',
        'unit_id',
        'min_stock_qty',
        'max_stock_qty',
        're_order_qty',
        'hsn_code',
        'rate_per_unit',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'

    ];
}
