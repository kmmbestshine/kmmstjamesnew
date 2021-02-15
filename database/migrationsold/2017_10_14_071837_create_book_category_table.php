<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $sql = 'CREATE TABLE book_category (id int(11) NOT NULL AUTO_INCREMENT, school_id int(11) NOT NULL, category varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, created_at  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, fine varchar(255) DEFAULT NULL,PRIMARY KEY (`id`));';  
      \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_category');
    }
}
