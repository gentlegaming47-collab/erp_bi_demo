<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taluka extends Model
{
    use HasFactory;

    protected $table = "talukas";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'taluka_name',
        'district_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];
}
