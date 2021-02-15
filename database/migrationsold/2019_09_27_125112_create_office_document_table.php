<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficeDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE office_document (
            id int(11) NOT NULL AUTO_INCREMENT,
             school_id int(11) NOT NULL, 
             user_id int(11) NOT NULL, 
           name varchar(200) NOT NULL, 
           image varchar(200) NOT NULL,
           pdf varchar(200) NOT NULL,
           date varchar(30) NOT NULL,
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",
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
        Schema::drop('office_document');
    }
}
