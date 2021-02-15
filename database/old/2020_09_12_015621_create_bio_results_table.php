<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bio_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id');
            $table->integer('teacher_type');
            $table->integer('total_marks');
            $table->text('answers');
            $table->timestamp('submission_time');
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
        Schema::drop('bio_results');
    }
}
