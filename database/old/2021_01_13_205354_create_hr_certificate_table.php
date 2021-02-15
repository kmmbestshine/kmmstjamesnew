<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHrCertificateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_certificate', function (Blueprint $table) {
            $table->increments('id');
            $table->string('school_id');
            $table->string('session_id');
            $table->string('certificate_id');
            $table->string('reg_no')->nullable();
            $table->string('from_class')->nullable();
            $table->string('to_class')->nullable();
            $table->string('from_year')->nullable();
            $table->string('to_year')->nullable();
            $table->string('att_percent')->nullable();
            $table->string('remarks')->nullable();
            $table->string('from_date')->nullable();
            $table->string('to_date')->nullable();
            $table->string('paid')->nullable();
             $table->string('to_be_paid')->nullable();
            $table->string('marks_percent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('hr_certificate');
    }
}
