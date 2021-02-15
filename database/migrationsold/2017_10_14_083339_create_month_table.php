<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE month (`id` int(11) NOT NULL AUTO_INCREMENT, month varchar(50) NOT NULL,PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
	   }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('month');
    }
}
