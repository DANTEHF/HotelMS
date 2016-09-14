<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('balance', function (Blueprint $table) {
            $table->increments('id');
           
            $table->string('order_number')->index()->unique();
            $table->string('type',4);
            $table->integer('deposit');
            $table->integer('balance1');
            $table->integer('balance2');
            $table->dateTime('time');
           
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
