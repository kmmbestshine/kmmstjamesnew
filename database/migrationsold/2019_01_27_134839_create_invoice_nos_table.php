<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceNosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql= 'CREATE TABLE invoice_nos (
            id int(10) NOT NULL AUTO_INCREMENT, 
            school_id int(10) unsigned NOT NULL, 
             session_id int(10) unsigned NOT NULL,
            class varchar(10) CHARACTER SET latin1 NOT NULL,
            student_id int(10) NOT NULL,
            invoice_id varchar(20) NOT NULL , 
            created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
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
        Schema::drop('invoice_nos');
    }
}
