<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierAgreement extends Model
{
    use HasFactory;

    protected $table = "supplier_agreement";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'supplier_id',
        'agreement_start_date',
        'agreement_end_date',
        'agreement_document',
        'cheque_no'
    ];
}
