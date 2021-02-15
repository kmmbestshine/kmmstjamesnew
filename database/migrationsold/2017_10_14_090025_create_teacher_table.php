<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE teacher (id int(11) NOT NULL AUTO_INCREMENT, user_id int(11) NOT NULL, school_id int(11) NOT NULL, type int(11) NOT NULL, class int(11) NOT NULL, section int(100) NOT NULL, name varchar(200) NOT NULL, mobile varchar(200) NOT NULL, email varchar(300) NOT NULL, salary varchar(200) DEFAULT NULL, avatar varchar(200) NOT NULL DEFAULT "default.jpg", platform varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));'; 
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher');
    }
}
