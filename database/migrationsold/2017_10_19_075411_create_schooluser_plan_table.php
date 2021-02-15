<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchooluserPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE schooluser_plan (id int(10) UNSIGNED NOT NULL, Modules varchar(255) COLLATE utf8_unicode_ci NOT NULL, Basic int(11) NOT NULL, Standard int(11) NOT NULL, Premium int(11) NOT NULL, created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL);';
	   \DB::connection()->getPdo()->exec($sql);
          }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schooluser_plan');
    }
}
