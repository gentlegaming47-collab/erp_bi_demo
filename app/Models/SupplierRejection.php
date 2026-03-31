<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRejection extends Model
{
    public $table = 'supplier_rejection_challan';

    public $timestamps = false;

    protected $primaryKey = 'src_id';

    protected $fillable = [
        'src_id',
        'src_type_id_fix',
        'src_type_value_fix',
        'current_location_id',
        'src_sequence',
        'src_number',
        'src_date',
        'supplier_id',
        'ref_no',
        'ref_date',
        'total_qty',
        'transporter_id',
        'vehicle_no',
        'lr_no',
        'lr_no_date',
        'lr_date',
        'special_notes',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
    ];
    use HasFactory;
}