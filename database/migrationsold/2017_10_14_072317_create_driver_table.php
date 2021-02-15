<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     $sql = 'CREATE TABLE driver (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, bus_id int(11) NOT NULL, driver_name varchar(100) NOT NULL, driver_mobile varchar(200) NOT NULL, driver_address text NOT NULL, driver_city  varchar(200) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", driver_user_name varchar(255) DEFAULT NULL, driver_user_pass varchar(100) NOT NULL, user_id int(11) NOT NULL,PRIMARY KEY (`id`));';
      \DB::connection()->getPdo()->exec($sql);
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver');
    }
}
