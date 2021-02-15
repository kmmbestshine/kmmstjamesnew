<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioDemoclassChklstTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE bio_democlass_chklst (
            id int(11) NOT NULL AUTO_INCREMENT, 
            staff_id int(11) NOT NULL,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,  
            demo_chklst varchar(50) NOT NULL,
            chklst_marks varchar(50) NOT NULL,
            chklst varchar(50) NOT NULL,
            chklst_val varchar(50) NOT NULL,
            remarks text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
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
        Schema::drop('bio_democlass_chklst');
    }
}
