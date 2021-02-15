<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     $sql = 'CREATE TABLE fee (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, frequency_id int(11) NOT NULL, registration_no varchar(20)NOT NULL, months varchar(200) NOT NULL, platfom varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
     \DB::connection()->getPdo()->exec($sql);   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee');
    }
}
