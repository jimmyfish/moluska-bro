<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankList extends Model
{
    protected $primaryKey = 'bank_id';   
    protected $table = 'bank_list';

    const CREATED_AT = "bank_create_date";
    const UPDATED_AT = "bank_update_date";

    protected $hidden = [
        'bank_del_status',
        'bank_update_by',
        'bank_flg_available',
        'bank_image',
        'bank_create_by',
        'bank_create_date',
        'bank_update_date',
        'bank_name_iris'
    ];
}
