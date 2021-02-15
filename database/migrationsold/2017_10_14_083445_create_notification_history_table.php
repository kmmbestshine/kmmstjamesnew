<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE notification_history (id int(11) NOT NULL AUTO_INCREMENT, notification_type_id int(11) NOT NULL, date varchar(20) NOT NULL, role_id int(11) NOT NULL, role enum("student","teacher","parent") NOT NULL, message_type enum("push_msg","text_msg") NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", school_id int(11) DEFAULT NULL,PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_history');
    }
}
