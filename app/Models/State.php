<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    /**
     * Table name
     */
    protected $table = "states";

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
        'state_name',
        'gst_code',
        'country_id',
        'company_id',
        'created_by_user_id',
        'last_by_user_id',
        'locked_by_user_id',
        'created_on',
        'last_on',
        'locked_on'
    ];
}
