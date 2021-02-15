<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIssueProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE issue_product (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL, 
            user_id int(11) NOT NULL, 
            work_typeid varchar(20) NOT NULL, 
            build_id varchar(20) NOT NULL,
            product_id varchar(20) NOT NULL,
            contractor_id varchar(20) NOT NULL,
            product_companyid varchar(20) NOT NULL,
            issue_date varchar(100) NOT NULL,
            issue_qty varchar(20) NOT NULL,
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
        Schema::drop('issue_product');
    }
}
