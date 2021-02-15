<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE students_notification (id int(10) unsigned NOT NULL AUTO_INCREMENT, notification_type_id int(11) DEFAULT NULL, student_id int(11) DEFAULT NULL, title varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, content varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, seen tinyint(1) DEFAULT NULL, created_at timestamp NULL DEFAULT NULL, updated_at timestamp NULL DEFAULT NULL, class_id int(11) DEFAULT NULL, seen_users_id varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
	}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_notification');
    }
}
