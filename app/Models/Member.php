<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_code',
        'name',
        'address',
        'phone',
        'email',
        'registered_date',
        'expired_date',
    ];
}
