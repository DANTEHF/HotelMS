<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('room', function (Blueprint $table) {
           $table->increments('id');
           
            $table->string('room_id',12)->unique();
            $table->string('type',4);
            $table->string('location',20);
            $table->integer('price');
            $table->string('remark',60);
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
