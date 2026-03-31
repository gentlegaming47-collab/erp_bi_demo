<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerContacts extends Model
{
    use HasFactory;

    
    protected $table = "dealer_contacts";

    public $timestamps = false;

    protected $fillable = [
        'id', 
        'dealer_id',
        'contact_person',
        'contact_mobile_no',
        'contact_email'
    ];
}
