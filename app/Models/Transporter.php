<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    use HasFactory;

    protected $table = "transporters";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'transporter_name',
        'address',
        'pan',
        'gstin',
        'type_of_vehicle',
        'contact_person',
        'contact_person_mobile',
        'contact_person_email_id',
        'payment_terms',
        'status',
        'approval_pending',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];


}