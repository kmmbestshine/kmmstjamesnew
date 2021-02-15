<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeSystemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $sql = 'CREATE TABLE grade_system ( id int(11) NOT NULL AUTO_INCREMENT, exam_type_id int(11) NOT NULL, school_id int(11) NOT NULL, from_marks int(3) NOT NULL, to_marks int(3) NOT NULL, grade varchar(5) NOT NULL, result varchar(5) NOT NULL,PRIMARY KEY (`id`));';
	  \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grade_system');
    }
}
