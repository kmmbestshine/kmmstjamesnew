<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioDocuUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE bio_docu_upload (
            id int(11) NOT NULL AUTO_INCREMENT, 
            staff_id int(11) NOT NULL,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,
            school_name varchar(50) NOT NULL,
            degree varchar(50) NOT NULL,
            title varchar(30) NOT NULL,
            certNo varchar(30)  NULL,
            serNo varchar(20)  NULL,
            issuedt varchar(20) NOT NULL,
            aadharimage varchar(200)  NULL,
            aadharpdf varchar(200)  NULL,
            panimage varchar(200)  NULL,
            panpdf varchar(50)  NULL,
            bankimage varchar(200)  NULL,
            bankpdf varchar(200)  NULL,
            expimage varchar(200)  NULL,
            exppdf varchar(200)  NULL,
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
        Schema::drop('bio_docu_upload');
    }
}
