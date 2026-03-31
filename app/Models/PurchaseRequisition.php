<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $table = "purchase_requisition";

    public $timestamps =  false;

    protected $primaryKey = 'pr_id';


    protected $fillable = [
        'pr_id',
        'pr_form_id_fix',
        'pr_form_value_fix',
        'current_location_id',
        'pr_sequence',
        'pr_number',
        'pr_date',
        'supplier_id',
        'to_location_id',
        'prepared_by',
        'special_notes',
        'supplier_no_item_mapping_required',        
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