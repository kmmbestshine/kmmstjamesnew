<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRouteDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql= 'CREATE TABLE route_details (
            id int(10) NOT NULL AUTO_INCREMENT, 
            school_id int(10) unsigned NOT NULL, 
            session_id varchar(10) CHARACTER SET latin1 NOT NULL, 
            bus_id int(10) unsigned NOT NULL,
            route_name varchar(50) CHARACTER SET latin1 NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
            updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('route_details');
    }
}
