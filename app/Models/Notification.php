<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $table = "notification";

    protected $fillable = [
        'title',
        'content',
        'notifiable_id',
        'notifiable_type',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'notifiable_type',
        'notifiable_id'
    ];

    protected $casts = [
        'notifiable_id' => 'int'
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}
