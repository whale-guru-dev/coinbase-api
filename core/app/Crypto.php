<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crypto extends Model
{
    //
    protected $table = "crypto";

    protected $fillable = [
        'who', 'address', 'coin', 'amount', 'trxid', 'tm', 'sig', 'notification', 'hash','user', 'details', 'type', 'status'
    ];

    public $timestamps = false;
}
