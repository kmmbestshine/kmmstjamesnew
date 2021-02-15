<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamTimetableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     $sql = 'CREATE TABLE exam_timetable (id int NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, exam_type_id int(11) NOT NULL, class_id int NOT NULL, section_id int(11) NOT NULL, subject_id int(11) NOT NULL, teacher_id int(11) NOT NULL, start_time varchar(100) NOT NULL, end_time varchar(100) NOT NULL, exam_date varchar(100) NOT NULL, platfom varchar(100) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,PRIMARY KEY (`id`));';
      \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_timetable');
    }
}
