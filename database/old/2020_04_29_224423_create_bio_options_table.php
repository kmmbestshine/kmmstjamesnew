<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE bio_options (
            id int(11) NOT NULL AUTO_INCREMENT, 
            question_id int(11) NOT NULL, 
            option varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
            avatar text  NULL,
            correct enum("0","1") NOT NULL, 
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bio_options');
    }
}
