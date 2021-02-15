<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioExpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE bio_exp (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,
            staff_id int(11) NOT NULL,
            type varchar(50) NOT NULL,
            institute_name varchar(100) NOT NULL,
            from_dt varchar(50) NOT NULL,
            to_dt varchar(50) NOT NULL,
            tenure varchar(70) NOT NULL,
            salary varchar(50) NOT NULL, 
            ref_contact varchar(20)  NULL,
            ref_rank varchar(50)  NULL,
            ref_name varchar(50)  NULL,
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
        Schema::drop('bio_exp');
    }
}
