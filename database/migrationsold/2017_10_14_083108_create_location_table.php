<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $sql = 'CREATE TABLE location (id int(11) NOT NULL AUTO_INCREMENT, latitude varchar(255) DEFAULT NULL, longitude varchar(255) DEFAULT NULL, user_name varchar(255) DEFAULT NULL, phone_number varchar(255) DEFAULT NULL, session_id varchar(255) DEFAULT NULL, speed varchar(255) DEFAULT NULL, direction varchar(255) DEFAULT NULL, distance varchar(255) DEFAULT NULL, gps_time datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, location_method varchar(255) DEFAULT NULL, accuracy varchar(255) DEFAULT NULL, extra_info varchar(255) DEFAULT NULL, event_type varchar(255) DEFAULT NULL,PRIMARY KEY (`id`));';
       \DB::connection()->getPdo()->exec($sql);  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location');
    }
}
