<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     $sql = 'CREATE TABLE issue (id int(11) NOT NULL AUTO_INCREMENT, book_id int(11) NOT NULL, student_id int(11) NOT NULL, school_id int(11) NOT NULL, teacher_name varchar(100) NOT NULL, issue_date varchar(100) NOT NULL, return_date varchar(50) NOT NULL, fine int(11) NOT NULL, return_flag int(11) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
     \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issue');
    }
}
