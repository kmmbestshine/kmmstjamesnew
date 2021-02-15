<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /*  $sql = 'CREATE TABLE timetable (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, class_id int(11) NOT NULL, 
		section_id int(11) NOT NULL, subject_id int(11) NOT NULL, teacher_id int(11) NOT NULL,
		period varchar(50) NOT NULL,
		start_time varchar(100) NOT NULL,
		end_time varchar(50) NOT NULL,
		day varchar(50) NOT NULL,
		platfom varchar(50) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));'; */
	   /*  \DB::connection()->getPdo()->exec($sql);	 */
		Schema::create('time-table', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('school_id');
			$table->integer('class_id');
			$table->integer('section_id');
			$table->integer('subject_id');
			$table->integer('teacher_id');
			$table->string('period', 100);
			$table->string('start_time', 100);
			$table->string('end_time', 100);
			$table->string('day', 100);
			$table->string('platfom', 100);
			$table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
