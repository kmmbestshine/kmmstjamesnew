<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioSelectedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE bio_selected (
            id int(11) NOT NULL AUTO_INCREMENT, 
            staff_id int(11) NOT NULL,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,
            school_name varchar(50) NOT NULL,  
            doj varchar(50) NOT NULL,
            designation varchar(50) NOT NULL,
            period varchar(50)  NULL,
            salary varchar(50)  NULL,
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
        Schema::drop('bio_selected');
    }
}
