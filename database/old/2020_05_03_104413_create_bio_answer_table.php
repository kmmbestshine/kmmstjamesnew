<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = 'CREATE TABLE bio_answer (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL, 
            staff_id int(11) NOT NULL,
            type varchar(50) NOT NULL,
            qns_id int(11) NOT NULL, 
            en_answer varchar(50) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
            PRIMARY KEY (`id`),KEY id (`id`));';
         \DB::connection()->getPdo()->exec($sql);  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bio_answer');
    }
}
