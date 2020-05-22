<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $table = "address";

    protected $fillable = [
        'who', 'byy', 'sig', 'amount', 'typ', 'tm', 'trxid', 'msg', 'fromWhom'
    ];

    public $timestamps = false;
}
