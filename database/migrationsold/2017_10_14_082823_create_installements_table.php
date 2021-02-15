<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE installments (id int(10) NOT NULL AUTO_INCREMENT, school_id int(10) unsigned NOT NULL, amount varchar(30) CHARACTER SET latin1 NOT NULL, due_date date DEFAULT NULL, Installment_type varchar(30) CHARACTER SET latin1 NOT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);     		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installements');
    }
}
