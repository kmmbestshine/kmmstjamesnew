<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE leave_request (id int(11) NOT NULL AUTO_INCREMENT, student_id int(11) NOT NULL, user_id int(11) NOT NULL, school_id int(11) NOT NULL, from_leave varchar(50) NOT NULL, to_leave varchar(50) NOT NULL, status enum("approved","cancelled","process") NOT NULL, by_request varchar(100) NOT NULL, view_status int(2) NOT NULL, attendance_session varchar(50) NOT NULL, remarks text NOT NULL, teacher_remarks text NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_request');
    }
}
