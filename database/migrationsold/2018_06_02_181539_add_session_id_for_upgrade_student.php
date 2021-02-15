<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionIdForUpgradeStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('class', function (Blueprint $table)
        {
            $table->integer('session_id')->after('school_id');
        });
        Schema::table('section', function (Blueprint $table)
        {
            $table->integer('session_id')->after('school_id');
        });
        Schema::table('time-table', function (Blueprint $table)
        {
            $table->integer('session_id')->after('school_id')->default(0);
        });
        Schema::table('exam_timetable', function (Blueprint $table)
        {
            $table->integer('session_id')->after('school_id')->default(0);
        });
        Schema::table('result', function (Blueprint $table)
        {
            $table->integer('session_id')->after('school_id')->default(0);
        });

        Schema::table('teacher', function (Blueprint $table)
        {
            $table->integer('session_id')->after('school_id')->default(0);
            $table->string('designation')->after('salary')->nullable();
        });

        Schema::table('grade_system', function (Blueprint $table)
        {
            $table->text('remarks')->after('result');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
