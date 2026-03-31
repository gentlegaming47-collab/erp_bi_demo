<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    use HasFactory;

    public $table = 'log_activity';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'operation',
        'section',
        'item_id',
        'message',
        'line_number'
    ];

}