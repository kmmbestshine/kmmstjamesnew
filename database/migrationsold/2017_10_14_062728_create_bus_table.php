<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        $sql = 'CREATE TABLE bus ( id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, bus_no varchar(50) NOT NULL, bus_type varchar(50) NOT NULL, bus_owned_by varchar(50) NOT NULL, gps_no varchar(200) DEFAULT NULL, route varchar(200) NOT NULL, city varchar(200) NOT NULL, capacity varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", PRIMARY KEY (`id`));'; 
        \DB::connection()->getPdo()->exec($sql);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus');
    }
}
