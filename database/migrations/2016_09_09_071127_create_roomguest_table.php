<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomguestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('roomguest', function (Blueprint $table) {
            $table->increments('id');
            
             $table->string('order_number');
             $table->index('order_number')->unique();
             $table->string('room_id');
             $table->string('id_number',18);
             $table->dateTime('in_time');
             $table->dateTime('out_time');
             $table->integer('nights');
             $table->integer('deposit');
             $table->integer('status');
            
          
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
