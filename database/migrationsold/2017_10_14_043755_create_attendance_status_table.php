<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = 'CREATE TABLE attendance_status (id int(11) NOT NULL AUTO_INCREMENT, date date NOT NULL, school_id int(11) NOT NULL, class_id varchar(100) NOT NULL, section_id varchar(100) NOT NULL, teacher_id varchar(100) NOT NULL, attendance_session varchar(3) NOT NULL,PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_status');
    }
}
