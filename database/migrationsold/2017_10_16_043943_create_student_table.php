<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE student (id int(11) NOT NULL AUTO_INCREMENT, user_id int(11) NOT NULL, session_id int(11) NOT NULL, class_id int(11) NOT NULL, section_id int(11) NOT NULL, school_id int(11) NOT NULL, parent_id int(11) NOT NULL, caste_id int(11) NOT NULL, blood_group varchar(20) NOT NULL, religion int(11) NOT NULL, bus_id int(11) NOT NULL, bus_stop_id int(11) NOT NULL, registration_no varchar(50) NOT NULL, roll_no varchar(50) NOT NULL, name varchar(200) NOT NULL, dob varchar(100) NOT NULL, date_of_admission varchar(50) NOT NULL, date_of_joining varchar(50) NOT NULL, gender varchar(50) NOT NULL, nationality varchar(50) NOT NULL, contact_no varchar(50) NOT NULL, email text NOT NULL, previous_school text, avatar text NOT NULL, pick_time varchar(100) NOT NULL, drop_time varchar(100) NOT NULL, documents text NOT NULL, platform varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", deleted_at timestamp NULL DEFAULT NULL,PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student');
    }
}
