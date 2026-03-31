<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemIssue extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_issue_id';

    public $timestamps = false;

    public $table = 'item_issue';

    protected $fillable = [
        'item_issue_id',
        'issue_type_id_fix',
        'issue_type_value_fix',
        'current_location_id',
        'issue_sequence',
        'issue_number',
        'issue_date',
        'supplier_id',
        'total_qty',
        'special_notes',
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
