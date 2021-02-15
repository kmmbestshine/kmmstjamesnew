<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSionfeeCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $sql = 'CREATE TABLE sionfee_collection (
            id int(10) NOT NULL AUTO_INCREMENT, 
            paid enum("true","false") COLLATE utf8mb4_unicode_ci NOT NULL, 
            fee_id int(10) NOT NULL,
            school_id int(10) NOT NULL, 
            session_id int(20) NOT NULL, 
            class varchar(10) NOT NULL,
            section varchar(10) NOT NULL,
            student_id int(10) NOT NULL, 
            name varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            reg_no varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            roll_no varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
            amount int(20) NOT NULL,
            balance_amount int(20) NOT NULL, 
            fee_name varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL, 
            fee_detail varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, 
            date date NOT NULL, 
            recived_by varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL, 
            paid_by varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL, 
            payment_type varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL, 
            payment_detail varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL, 
            late_fee int(10) NOT NULL, 
            concession int(10) NOT NULL, 
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
        Schema::drop('sionfee_collection');
    }
}
