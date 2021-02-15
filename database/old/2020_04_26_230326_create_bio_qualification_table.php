<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioQualificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE bio_qualification (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,
            staff_id int(11) NOT NULL,
            qualify varchar(100) NOT NULL,
            course_name varchar(100)  NULL,
            institute_name varchar(100)  NULL,
            year_passed varchar(50)  NULL,
            univer_board varchar(70)  NULL,
            marks_percent varchar(50)  NULL,
            typing varchar(50)  NULL,
            typ_lang varchar(50)  NULL,
            typ_qual varchar(50)  NULL,
            comp varchar(50) NULL,
            comp_lang varchar(50)  NULL,
            comp_qual varchar(50)  NULL,
             created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
             updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", 
             deleted_at timestamp NULL DEFAULT NULL,PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bio_qualification');
    }
}
