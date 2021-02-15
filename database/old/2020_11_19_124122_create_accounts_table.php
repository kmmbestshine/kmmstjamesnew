<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
             $table->integer('school_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('date')->nullable();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->string('expense_type')->nullable();
            $table->string('cash_deposit')->nullable();
            $table->string('income_type')->nullable();
            $table->double('amount')->nullable();
            $table->text('description')->nullable();
            $table->string('mode')->nullable();
            $table->string('cheq_no')->nullable();
            $table->string('cheq_date')->nullable();
            $table->string('cheq_bankname')->nullable();
            $table->string('trans_no')->nullable();
            $table->string('online_bankname')->nullable();
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
        Schema::drop('accounts');
    }
}
