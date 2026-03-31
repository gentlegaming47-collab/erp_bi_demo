<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierItemMapping extends Model
{
    use HasFactory;

    public $table = 'supplier_item_mapping';

    public $timestamps = false;

    protected $primaryKey = 'supplier_item_mapping_id';

    protected $fillable = [
        'supplier_item_mapping_id',
        'supplier_id',
        'item_id',
        'status',
        'year_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
    ];

}
