<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE users (id int(11) NOT NULL AUTO_INCREMENT, type enum("admin","principal","teacher","student","parent","school","user_role","driver") NOT NULL, school_id varchar(100) NOT NULL, username varchar(100) NOT NULL, password varchar(200) NOT NULL, hint_password varchar(200) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", remember_token varchar(50) NOT NULL, status int(11) NOT NULL DEFAULT "1", PRIMARY KEY (`id`));';
       \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
