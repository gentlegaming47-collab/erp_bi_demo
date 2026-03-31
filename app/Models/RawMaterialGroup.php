<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialGroup extends Model
{
    use HasFactory;

    protected $table = "raw_material_groups";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'raw_material_group_nm',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];

}
