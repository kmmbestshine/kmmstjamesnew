<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHomeworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE homework (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, class_id varchar(100) NOT NULL, section_id varchar(100) NOT NULL, subject_id varchar(100) NOT NULL,`teacher_id` varchar(100) DEFAULT NULL, description text NOT NULL, image varchar(200) NOT NULL,`pdf` varchar(200) NOT NULL,`date` varchar(50) NOT NULL, homework_by varchar(200) NOT NULL,`student_v_status` int(5) NOT NULL, parent_v_status int(5) NOT NULL, platform varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
	}
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('homework');
    }
}
