<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFurnitureDistributionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('furniture_distribution_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('purchase_id')->unsigned();
            $table->string('school_id');
            $table->string('user_id');
            $table->integer('class_id');
            $table->integer('section_id');
            $table->string('registration_no');
            $table->string('category');
            $table->string('sub_category');
            $table->string('item_name');
            $table->integer('quantity');
            $table->integer('avail_quantity');
            $table->decimal('distribute_rate');
            $table->string('amount');
            $table->string('comment');
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->foreign('purchase_id')->references('id')->on('furniture_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('furniture_distribution_details');
    }
}
