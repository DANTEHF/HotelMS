<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookroomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
          Schema::create('bookroom', function (Blueprint $table) {
           $table->increments('id');
            
            $table->string('name',6);
            $table->string('telephone',15);
            $table->date('booktime');
            $table->string('room_id',12);
            
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
