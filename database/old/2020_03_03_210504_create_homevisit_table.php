<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomevisitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql= 'CREATE TABLE homevisit (id int(10) NOT NULL AUTO_INCREMENT, 
            school_id int(10) unsigned NOT NULL, 
            session varchar(10) CHARACTER SET latin1 NOT NULL, 
            class_id int(10) unsigned NOT NULL, 
            section_id int(10) unsigned NOT NULL,
            student_id int(10) unsigned NOT NULL,
            `date` varchar(50) NOT NULL,
            teacher_name varchar(50) CHARACTER SET latin1 NOT NULL,
            enq_points varchar(100) CHARACTER SET latin1 NOT NULL, 
            en_status varchar(20) CHARACTER SET latin1 NOT NULL, 
            trouble_stud varchar(100) CHARACTER SET latin1 NOT NULL, 
            troub_status varchar(20) CHARACTER SET latin1 NOT NULL, 
            parents_points varchar(100) CHARACTER SET latin1 DEFAULT NULL, 
            parents_status varchar(20) CHARACTER SET latin1 NOT NULL, 
            othersone varchar(100) CHARACTER SET latin1 DEFAULT NULL,
            otherstwo varchar(100) CHARACTER SET latin1 DEFAULT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
            updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('homevisit');
    }
}
