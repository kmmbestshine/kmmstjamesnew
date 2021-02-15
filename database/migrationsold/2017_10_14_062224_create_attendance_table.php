<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        $sql = 'CREATE TABLE attendance (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, teacher_id varchar(100) DEFAULT NULL ,class_id varchar(100) NOT NULL ,section_id varchar(100) NOT NULL, student_id varchar(100) NOT NULL, attendance varchar(20) DEFAULT NULL, remarks varchar(20) NOT NULL, date varchar(20) NOT NULL, attendance_by varchar(100) NOT NULL, attendance_session varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", PRIMARY KEY (id));';
        \DB::connection()->getPdo()->exec($sql);
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance');
    }
}
