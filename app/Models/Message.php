<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    // Allow mass assignment
    protected $fillable = [
        'original',
        'filtered'
    ];
}