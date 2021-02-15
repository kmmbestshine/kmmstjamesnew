<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseNosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_nos', function (Blueprint $table) {
           $table->increments('id');
             $table->string('school_id')->nullable();
            $table->string('remarks')->nullable();
            $table->string('ventor_id')->nullable();
            $table->string('po_id');
            $table->double('totalamount')->nullable();
            $table->double('paidamount')->nullable();
            $table->double('dueamount')->nullable();
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
        Schema::drop('purchase_nos');
    }
}
