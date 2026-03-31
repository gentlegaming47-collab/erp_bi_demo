<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    public $table = 'material_request';

    protected $primaryKey = 'mr_id';

    public $timestamps = false;

    protected $fillable = [
        'current_location_id',
        'mr_sequence',
        'mr_number',
        'mr_date',
        'to_location_id',
        'customer_group_id',
        'total_qty',
        'special_notes',
        'sm_approvaldate',
        'sm_user_id',
        'sm_created_on',
        'state_coordinator_approvaldate',
        'state_coordinator_user_id',
        'state_coordinator_created_on',
        'zsm_approvaldate',
        'zsm_user_id',
        'zsm_created_on',
        'md_approvaldate',
        'md_user_id',
        'md_created_on',

        'gm_approvaldate',
        'gm_user_id',
        'gm_created_on',
        
        'approval_type_id_fix',
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