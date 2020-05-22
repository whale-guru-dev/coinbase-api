<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CryptoAddress extends Model
{
    protected $table = "transaction";

    protected $fillable = [
        'userEmail', 'btc', 'btc_more', 'eth', 'eth_more', 'etc', 'etc_more', 'bat', 'bat_more','ltc', 'ltc_more'
    ];

    public $timestamps = false;
}
