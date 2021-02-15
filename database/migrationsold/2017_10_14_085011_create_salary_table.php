<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $sql = 'CREATE TABLE salary (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, staff_type int(11) NOT NULL, employee_id int(11) NOT NULL, value int(11) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
      \DB::connection()->getPdo()->exec($sql);	  
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary');
    }
}
