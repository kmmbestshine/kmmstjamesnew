<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioPersonalInterviewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE bio_personal_interview (
            id int(11) NOT NULL AUTO_INCREMENT, 
            staff_id int(11) NOT NULL,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,  
            pers_chklst varchar(50) NOT NULL,
            per_chklst_marks varchar(50) NOT NULL,
            chklst varchar(50)  NULL,
            chklst_valu varchar(50)  NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bio_personal_interview');
    }
}
