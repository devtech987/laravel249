<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CookieTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_of_cookie',
        'price',
        'user_id'
    ];
}
