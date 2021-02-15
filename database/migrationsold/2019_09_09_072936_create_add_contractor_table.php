<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddContractorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE add_contractor (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL, 
            user_id int(11) NOT NULL, 
            build_id int(11) NOT NULL,
            work_id int(11) NOT NULL,
            user_type varchar(100) NOT NULL, 
            contractor_name varchar(200) NOT NULL,
            labour_name varchar(200) NOT NULL, 
            phone_no varchar(100) NOT NULL,
            address varchar(200) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
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
        Schema::drop('add_contractor');
    }
}
