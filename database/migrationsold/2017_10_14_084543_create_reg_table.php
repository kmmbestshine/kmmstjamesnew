<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE reg (id int(11) NOT NULL AUTO_INCREMENT, reg_id longtext, type enum("android","ios") DEFAULT NULL, created_at timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reg');
    }
}
