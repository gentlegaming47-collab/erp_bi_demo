<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MisCategory extends Model
{
    use HasFactory;

    protected $table = "mis_category";

    public $timestamps = false;

    protected $fillable = [
        'mis_category',
        'company_id',
        'created_by_user_id',
        'last_by_user_id',
        'locked_by_user_id',
        'created_on',
        'last_on',
        'locked_on'
    ];


}
