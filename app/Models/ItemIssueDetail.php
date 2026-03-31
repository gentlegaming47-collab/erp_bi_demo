<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemIssueDetail extends Model
{
    protected $primaryKey = 'item_issue_details_id';

    public $timestamps = false;

    public $table = 'item_issue_details';

    protected $fillable = [
        'item_issue_details_id',
        'item_issue_id',
        'item_id',
        'item_details_id',
        'issue_qty',
        'item_type',
        'remarks',
        'status',
    ];
    use HasFactory;
    
  
}
