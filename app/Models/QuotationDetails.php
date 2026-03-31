<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetails extends Model
{
    use HasFactory;

    protected $table = "quotation_details";

    /**
     * disable laravel default timestamps field
     */
    public $timestamps    = false;

    protected $primaryKey = 'quot_details_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        
        'quot_id',
        'item_id',
        'quot_qty',
        'rate_per_unit',
        'quot_amount',       
        'status'
    ];
}
