<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionIdAndStaffTypeAndSessionTypeToTeacherAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_attendance', function (Blueprint $table) {
            $table->integer('session_id')->nullable()->after('school_id');
            $table->integer('staff_type')->nullable()->after('session_id');
            $table->string('session_type',500)->nullable()->after('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_attendance', function (Blueprint $table) {
            //
        });
    }
}
