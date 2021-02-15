<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeeStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql= 'CREATE TABLE fee_structure (id int(10) NOT NULL AUTO_INCREMENT, school_id int(10) unsigned NOT NULL, session varchar(10) CHARACTER SET latin1 NOT NULL, class_id int(10) unsigned NOT NULL, fees_name varchar(100) CHARACTER SET latin1 NOT NULL, student_type varchar(20) CHARACTER SET latin1 NOT NULL, payment_type varchar(20) CHARACTER SET latin1 NOT NULL, amount varchar(30) CHARACTER SET latin1 NOT NULL, installment_id varchar(100) CHARACTER SET latin1 DEFAULT NULL, created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00",PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);		
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fee_structure');
    }
}
