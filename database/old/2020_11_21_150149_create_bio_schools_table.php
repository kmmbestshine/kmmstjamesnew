<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE bio_schools (
            id int(11) NOT NULL AUTO_INCREMENT, 
         school_name varchar(300) NOT NULL, 
         email text NOT NULL, 
         mobile varchar(100) NOT NULL, 
         address varchar(200) NOT NULL, 
         city varchar(100) NOT NULL, 
         image text NOT NULL, 
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
        Schema::drop('bio_schools');
    }
}
