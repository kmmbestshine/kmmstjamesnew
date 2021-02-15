<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     $sql = 'CREATE TABLE parent (id int(11) NOT NULL AUTO_INCREMENT, user_id int(11) NOT NULL, school_id int(11) NOT NULL, state varchar(100) NOT NULL, name varchar(200) NOT NULL, mother varchar(200) NOT NULL, mobile varchar(200) NOT NULL,`email` varchar(200) DEFAULT NULL, father_occupation varchar(200) DEFAULT NULL, mother_occupation varchar(200) DEFAULT NULL, city varchar(100) NOT NULL, address text, pin_code varchar(100) NOT NULL, avatar text NOT NULL, platform varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
     \DB::connection()->getPdo()->exec($sql);	 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parent');
    }
}
