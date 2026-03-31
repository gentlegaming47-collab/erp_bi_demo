<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequestDetail extends Model
{
    use HasFactory;

    public $table = 'material_request_details';

    protected $primaryKey = 'mr_details_id';

    public $timestamps = false;

    protected $fillable = [
        'mr_id',
        'item_id',
        'form_type',
        'mr_qty',
        'rate_unit',
        'remarks',
        'sm_approvaldate',
        'sm_user_id',
        'sm_created_on',
        'zsm_approvaldate',
        'zsm_user_id',
        'zsm_created_on',
        'md_approvaldate',
        'md_user_id',
        'md_created_on',
        'approval_type_id_fix',
        'status'
    ];

}