<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE result (id int(11) NOT NULL AUTO_INCREMENT, class_id int(11) NOT NULL, section_id int(11) NOT NULL, exam_type_id int(11) NOT NULL, month varchar(100) NOT NULL, subject_id int(11) NOT NULL, student_id int(11) NOT NULL, teacher_id int(11) DEFAULT NULL, date varchar(50) NOT NULL, max_marks varchar(10) NOT NULL, pass_marks varchar(10) NOT NULL, obtained_marks varchar(100) NOT NULL, result varchar(20) NOT NULL, grade varchar(10) NOT NULL, rank varchar(50) NOT NULL, result_by varchar(100) NOT NULL, view_status_s int(2) NOT NULL, view_status int(2) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
		 \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result');
    }
}
