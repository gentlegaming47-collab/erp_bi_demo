<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReplacementItemDecisionDetails extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = "replacement_item_decision_details";

    public $timestamps = false;

    protected $primaryKey = 'replacement_details_id';

    protected $fillable = [
        'replacement_details_id',
        'replacement_id',        
        'so_mapping_details_id',
        'item_id',
        'decision_qty',       
        'item_details_id',       
        'decision_detail_qty',       
        'status',       
    ];
}