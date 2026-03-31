<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GRNMaterial extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $table = 'grn_material_receipt';

    protected $primaryKey = 'grn_id';

    protected $fillable = [
        'grn_type_id_fix',
        'grn_type_value_fix',
        'current_location_id',
        'grn_sequence',
        'grn_number',
        'grn_date',
        'supplier_id',
        'to_location_id',
        'bill_no',
        'bill_date',
        'total_qty',
        'total_amount',
        'transporter_id',
        'vehicle_no',
        'lr_no_date',
        'special_notes',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on',
    ];
}