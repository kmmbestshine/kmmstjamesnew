<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCheqNoAndCheqDateAndBankNameAndOnlineBanknameAndTrnNoAndPaymentModeToExpenditureDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('expenditure_details', function (Blueprint $table) {
            $table->integer('cheque_no')->after('amount');
            $table->string('cheque_date')->nullable()->after('cheque_no');
            $table->string('bank_name')->nullable()->after('cheque_date');
            $table->string('online_bankname')->nullable()->after('bank_name');
            $table->string('transaction_no')->nullable()->after('online_bankname');
            $table->string('payment_mode')->after('transaction_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenditure_details', function (Blueprint $table) {
            //
        });
    }
}
