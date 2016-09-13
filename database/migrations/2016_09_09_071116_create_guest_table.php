<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
           Schema::create('guest', function (Blueprint $table) {
          $table->increments('id');
            
            $table->string('id_number',18);
            $table->string('name',6);
            $table->string('sex',2);
            $table->string('type',2);
            $table->string('telephone');
            $table->integer('vip');
            $table->index('id_number')->unique();
           
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
