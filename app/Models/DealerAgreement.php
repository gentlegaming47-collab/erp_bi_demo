<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerAgreement extends Model
{
    use HasFactory;

    protected $table = "dealer_agreement";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'dealer_id',
        'agreement_start_date',
        'agreement_end_date',
        'agreement_document',
        'cheque_no'
    ];
}
