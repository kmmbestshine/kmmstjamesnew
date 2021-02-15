<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE add_product (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL, 
            user_id int(11) NOT NULL, 
            work_typeid varchar(200) NOT NULL, 
            build_id varchar(100) NOT NULL,
            receipt_no varchar(100) NOT NULL,
            product_name varchar(100) NOT NULL,
            supplier_name varchar(100) NOT NULL,
            product_company varchar(100) NOT NULL,
            price varchar(100) NOT NULL,
            pur_date varchar(100) NOT NULL,
            quantity varchar(100) NOT NULL,
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
        Schema::drop('add_product');
    }
}
