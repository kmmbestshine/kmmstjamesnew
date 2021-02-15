<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE push_notification (id int(11) NOT NULL AUTO_INCREMENT, device_id text NOT NULL, role_id int(11) NOT NULL, title varchar(200) NOT NULL, description text NOT NULL, image varchar(200) NOT NULL, role enum("teacher","parent","student") NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", is_live int(11) unsigned zerofill DEFAULT NULL COMMENT "0",PRIMARY KEY (`id`));'; 
		 \DB::connection()->getPdo()->exec($sql);
	}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('push_notification');
    }
}
