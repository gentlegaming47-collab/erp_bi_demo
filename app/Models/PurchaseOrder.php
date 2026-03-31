<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = "purchase_order";

    public $timestamps =  false;

    protected $primaryKey = 'po_id';


    protected $fillable = [
        'po_id',
        'current_location_id',
        'po_sequence',
        'po_number',
        'po_date',
        'supplier_id',
        'person_name',
        'order_by',
        'ref_no',
        'ref_date',
        'to_location_id',
        'delivery_date',
        'total_qty',
        'total_amount',
        'pf_charge',
        'frieght',
        'gst',
        'test_certificate',
        'order_acceptance',
        'prepared_by',
        'payment_terms',
        'special_notes',
        'is_approved',
        'po_form_type',
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