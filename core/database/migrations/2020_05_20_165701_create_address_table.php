<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('address', function (Blueprint $table) {
            $table->increments('id');
            $table->string('userEmail');
            $table->string('btc');
            $table->string('btc_more');
            $table->string('eth');
            $table->string('eth_more');
            $table->string('etc');
            $table->string('etc_more');
            $table->string('bat');
            $table->string('bat_more');
            $table->string('ltc');
            $table->string('ltc_more');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address');
    }
}
