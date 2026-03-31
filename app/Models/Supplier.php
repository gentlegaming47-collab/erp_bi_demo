<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = "suppliers";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'supplier_name',
        'supplier_code',
        'address',
        'village_id',
        'pincode',
        'contact_person',
        'contact_person_mobile',
        'contact_person_email_id',
        'web_address',
        'GSTIN',
        'payment_terms',
        'status',
        'approval_status',
        'no_item_mapping_required',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];

}