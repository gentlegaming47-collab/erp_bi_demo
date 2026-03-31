<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = "districts";

    /**
     * disable laravel default timestamps field
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'district_name',
        'state_id',
        'company_id',
        'created_by_user_id',
        'last_by_user_id',
        'locked_by_user_id',
        'created_on',
        'last_on',
        'locked_on'
    ];
}
