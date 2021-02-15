<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultModTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE result_mod (id int(11) NOT NULL AUTO_INCREMENT, student_id int(11) NOT NULL, exam_type_id int(11) NOT NULL, month varchar(50) NOT NULL, teacher_id int(11) DEFAULT NULL, date varchar(50) NOT NULL, result int(50) NOT NULL, grade varchar(50) NOT NULL, remarks text NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('result_mod');
    }
}
