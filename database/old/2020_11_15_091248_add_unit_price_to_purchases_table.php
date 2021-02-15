<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitPriceToPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
           $table->string('unit_price')->after('units')->nullable();
            $table->string('remarks')->after('purchase_date')->nullable();
            $table->string('amount')->after('unit_price')->nullable();
            $table->string('school_id')->after('id')->nullable();
            $table->string('user_id')->after('school_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchases', function (Blueprint $table) {
            //
        });
    }
}
