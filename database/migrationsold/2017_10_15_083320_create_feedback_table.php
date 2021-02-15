<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE feedback (id int(11) NOT NULL AUTO_INCREMENT, student_id int(11) NOT NULL, school_id varchar(100) NOT NULL, teacher_id int(11) NOT NULL, feedback text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, date varchar(50) NOT NULL, platfom varchar(50) NOT NULL, feedback_by varchar(100) NOT NULL,`view_status` int(2) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
	   \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
