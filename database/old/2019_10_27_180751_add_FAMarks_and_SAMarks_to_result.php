<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFAMarksAndSAMarksToResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('result', function (Blueprint $table) {
           $table->string('fa_marks')->after('obtained_marks')->nullable();
             $table->string('sa_marks')->after('pass_marks')->nullable();
             $table->string('fa_grade')->after('grade')->nullable();
             $table->string('sa_grade')->after('fa_grade')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('result', function (Blueprint $table) {
            //
        });
    }
}
