<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusStopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
      $sql = 'CREATE TABLE bus_stop (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, bus_id int(11) NOT NULL, stop text NOT NULL, stop_index varchar(50) NOT NULL, lattitude varchar(50) NOT NULL, transport_fee varchar(255) DEFAULT NULL, longitude varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';		
      \DB::connection()->getPdo()->exec($sql);
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus_stop');
    }
}
