<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
             $table->increments('id');
            $table->string('goods_name');
            $table->string('party_name');
            $table->string('order_qty')->nullable();
            $table->string('purchased_qty')->nullable();
            $table->string('units')->nullable();
            $table->double('totalamount');
            $table->double('paidamount');
            $table->double('dueamount');
            $table->date('purchase_date');
            $table->string('created_by', 100);
            $table->string('modified_by', 100)->nullable();
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
        Schema::drop('purchases');
    }
}
