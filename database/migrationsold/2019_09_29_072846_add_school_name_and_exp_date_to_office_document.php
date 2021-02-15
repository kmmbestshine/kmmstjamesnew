<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSchoolNameAndExpDateToOfficeDocument extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('office_document', function (Blueprint $table) {
            $table->string('school_name')->after('name')->nullable();
             $table->string('exp_date')->after('date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('office_document', function (Blueprint $table) {
            //
        });
    }
}
