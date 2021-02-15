<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('session_id');
            $table->integer('school_id');
            $table->string('employee_id');
            $table->string('payment_id');
            $table->string('payment_date');
            $table->string('month');
            $table->string('year');
            $table->string('worked_days');
            $table->string('basic');
            $table->string('allowance');
            $table->string('overtime');
            $table->string('bonus');
            $table->string('ptax');
            $table->string('deductions');
            $table->string('allowed_leave');
            $table->string('earned_leave');
            $table->string('gross');
            $table->string('total_salary');
            //$table->timestamps();
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('employee_payrolls');
    }
}
