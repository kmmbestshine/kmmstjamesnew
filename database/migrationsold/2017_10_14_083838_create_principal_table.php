<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrincipalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          $sql = 'CREATE TABLE principal (id int(11) NOT NULL AUTO_INCREMENT, user_id int(11) NOT NULL, school_id int(11) NOT NULL, name varchar(200) NOT NULL, email varchar(200) NOT NULL, mobile varchar(100) NOT NULL, username varchar(100) NOT NULL, image varchar(200) NOT NULL, platfom varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
          \DB::connection()->getPdo()->exec($sql);		  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('principal');
    }
}
