<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractorPaidAmtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE contractor_paidAmt (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL, 
            paid enum("true","false") COLLATE utf8mb4_unicode_ci NOT NULL, 
            fee_id int(10) NOT NULL,
            user_id int(11) NOT NULL, 
            work_type varchar(20) NOT NULL, 
            build_id varchar(10) NOT NULL,
            contractor_id varchar(20) NOT NULL,
            labour_id varchar(20) NOT NULL,
            amount int(20) NOT NULL,
            balance_amount int(20) NOT NULL, 
            phone_no  varchar(20) NOT NULL,
            usertype   varchar(20) NOT NULL,
            fee_name varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL, 
            date date NOT NULL, 
            recived_by varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL, 
            paid_by varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
            cheque_no int(10)  NULL, 
            cheque_date varchar(50)  NULL, 
            bank_name varchar(50) CHARACTER SET latin1 DEFAULT NULL, 
            online_bankname varchar(50) CHARACTER SET latin1 DEFAULT NULL, 
            transaction_no varchar(50) CHARACTER SET latin1 DEFAULT NULL, 
            payment_mode varchar(20) CHARACTER SET latin1 DEFAULT NULL, 
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
        Schema::drop('contractor_paidAmt');
    }
}
