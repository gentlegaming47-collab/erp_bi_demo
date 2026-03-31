<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $table = "price_list";

    public $timestamps =  false;


    protected $fillable = [
        'customer_group_id',
        'company_id',
        'created_by_user_id',
        'created_on',
        'last_by_user_id',
        'last_on',
        'locked_by_user_id',
        'locked_on'
    ];

}
