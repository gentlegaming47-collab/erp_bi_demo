<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QCApproval extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = "qc_approval";

    public $timestamps =  false;

    protected $primaryKey = 'qc_id';


    protected $fillable = [
        'qc_id',
        'qc_sequence',
        'qc_number',
        'qc_date',
        'grn_details_id',
        'item_id',
        'item_details_id',
        'qc_qty',
        'ok_qty',
        'reject_qty',
        'rejection_reason',
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