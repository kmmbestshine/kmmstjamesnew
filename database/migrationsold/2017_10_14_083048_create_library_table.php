<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibraryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $sql = 'CREATE TABLE library (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, book_no varchar(20) NOT NULL, subject_id varchar(50) NOT NULL, book_name varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, book_category int(11) NOT NULL, auth_name varchar(200) NOT NULL, publisher_name varchar(255) NOT NULL, publish_year varchar(200) NOT NULL, price int(11) NOT NULL, purchase_date varchar(200) NOT NULL, available int(11) NOT NULL, no_of_books int(15) NOT NULL, issued_books int(11) NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
		\DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('library');
    }
}
