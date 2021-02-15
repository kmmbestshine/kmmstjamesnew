<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE school (id int(11) NOT NULL AUTO_INCREMENT, status enum("Final","Demo") DEFAULT NULL, user_id int(11) NOT NULL, school_name varchar(200) NOT NULL, email text NOT NULL, mobile varchar(100) NOT NULL, address varchar(200) NOT NULL, city varchar(100) NOT NULL, image text NOT NULL, deleted_at timestamp NULL DEFAULT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", userplan varchar(150) NOT NULL, userplanAdded varchar(155) NOT NULL, schoolcategory int(11) NOT NULL, schoolstatus int(11) NOT NULL,PRIMARY KEY (`id`));'; 
	   \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school');
    }
}
