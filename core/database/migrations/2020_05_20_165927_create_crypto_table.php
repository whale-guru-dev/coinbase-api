<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCryptoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crypto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('who');
            $table->string('address');
            $table->string('coin');
            $table->string('amount');
            $table->string('trxid');
            $table->string('tm');
            $table->string('sig');
            $table->string('notification');
            $table->string('hash');
            $table->string('user');
            $table->string('details');
            $table->string('type');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto');
    }
}
