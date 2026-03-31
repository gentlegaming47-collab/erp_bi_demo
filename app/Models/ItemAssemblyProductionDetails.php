<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAssemblyProductionDetails extends Model
{
    use HasFactory;

    public $table = 'item_assembly_production_details';

    protected $primaryKey = 'iap_details_id';

    public $timestamps = false;

    protected $fillable = [
        'iap_id',
        'item_id',
        'raw_material_qty',
        'consumption_qty',
    ];

}
