<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddBuildworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE add_buildwork (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL, 
            user_id int(11) NOT NULL, 
            work_type varchar(200) NOT NULL, 
            contractor_name varchar(200) NOT NULL, 
            phoneno  varchar(20) NOT NULL, 
            address  varchar(200) NOT NULL, 
            build_id varchar(10) NOT NULL,
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
        Schema::drop('add_buildwork');
    }
}
