<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBiodataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = 'CREATE TABLE biodata (
            id int(11) NOT NULL AUTO_INCREMENT,
            school_id int(11) NOT NULL,
            session_id int(11) NOT NULL,
            name varchar(100) NOT NULL,
            contact_no varchar(20) NOT NULL,
            gender varchar(50) NOT NULL, 
            email text COLLATE utf8_unicode_ci DEFAULT NULL,
            whatsapp_no varchar(50)  NULL,
            facebook_id varchar(50)  NULL,
            instagram varchar(50)  NULL, 
            religion int(11)  NULL,
            caste_id int(11)  NULL,
             blood_group varchar(20)  NULL,
             f_name varchar(100)  NULL,
             f_contact_no varchar(20)  NULL,
             m_name varchar(100)  NULL,
             m_contact_no varchar(20)  NULL,
            address text, 
              pin_code varchar(10)  NULL, 
              avatar text  NULL,
              dob varchar(20)  NULL, 
              age varchar(20)  NULL,
              maried_sta varchar(30)  NULL, 
              spouse_name varchar(100) NULL,
             spouse_contact_no varchar(20)  NULL,
             language_known text NULL,
             created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
             updated_at timestamp NOT NULL DEFAULT "0000-00-00 00:00:00", 
             deleted_at timestamp NULL DEFAULT NULL,PRIMARY KEY (`id`));';
        \DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('biodata');
    }
}
