<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GuestBook extends Model
{
    use SoftDeletes;

    protected $table = "guest_book";
    
    protected $hidden = [
        'deleted_at',
        'updated_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone_number'
    ];
}
