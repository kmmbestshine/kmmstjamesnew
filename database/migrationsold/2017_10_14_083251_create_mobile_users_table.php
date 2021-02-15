<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE mobile_users (id int(10) unsigned NOT NULL AUTO_INCREMENT, user_type_id int(11) DEFAULT NULL, logged_in_count int(11) DEFAULT NULL, logged_in_date varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL,PRIMARY KEY (`id`));';
       \DB::connection()->getPdo()->exec($sql);		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_users');
    }
}
