<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;


     /**
     * Table name
     */
    protected $table = "admin";

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
        'user_name',
        'user_code',
        'password',
        'email_id',
        'mobile_no',
        'person_name',
        'status',
        'allow_multiple_veh_entry',
        'user_type',
        'signature_image',
        'company_id',
        'created_by_user_id',
        'last_by_user_id',
        'locked_by_user_id',
        'created_on',
        'last_on',
        'locked_on',
    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // protected $hidden = [
    //     'password',
    // ];
    protected $hidden = [
        'password',
    ];
}