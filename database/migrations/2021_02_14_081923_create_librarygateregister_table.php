<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrarygateregisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('librarygateregister', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date');
            $table->string('type');
            $table->string('username');
            $table->string('name');
            $table->string('class')->nullable();
            $table->string('section')->nullable();
            $table->string('staff_type')->nullable();
            $table->string('intime');
            $table->string('outtime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('librarygateregister');
    }
}
