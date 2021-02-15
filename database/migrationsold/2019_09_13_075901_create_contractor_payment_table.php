<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorPaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE contractor_payment (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL, 
            user_id int(11) NOT NULL, 
            work_typeid varchar(20) NOT NULL, 
            build_id varchar(10) NOT NULL,
            contractor_id varchar(20) NOT NULL,
            labour_id varchar(20) NOT NULL,
            date varchar(20) NOT NULL,
            contractor_amt  varchar(20) NOT NULL,
            amount varchar(20) NOT NULL,
            bal_status  varchar(20) NOT NULL,
            phone_no  varchar(20) NOT NULL,
            contractor_name  varchar(50) NOT NULL,
            user_type  varchar(50) NOT NULL,
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
        Schema::drop('contractor_payment');
    }
}
