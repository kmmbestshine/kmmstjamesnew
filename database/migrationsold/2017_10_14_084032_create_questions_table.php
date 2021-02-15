<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = 'CREATE TABLE questions (id int(11) NOT NULL AUTO_INCREMENT, class_id int(11) NOT NULL, school_id int(11) NOT NULL, question text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`),KEY id (`id`));';
         \DB::connection()->getPdo()->exec($sql);		 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
